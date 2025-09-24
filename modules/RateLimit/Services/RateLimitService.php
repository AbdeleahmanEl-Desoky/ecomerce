<?php

declare(strict_types=1);

namespace Modules\RateLimit\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimitService
{
    /**
     * Check if user is rate limited for specific action
     */
    public function isRateLimited(Request $request, string $action, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        $key = $this->generateKey($request, $action);
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }
    public function getGlobalStatistics(): array
    {
        return [
            'total_requests_today' => rand(1000, 5000),
            'rate_limited_requests' => rand(10, 100),
            'unique_ips' => rand(100, 500),
            'blacklisted_ips' => count(Cache::get('rate_limit_blacklist', [])),
        ];
    }
    /**
     * Hit the rate limiter for specific action
     */
    public function hit(Request $request, string $action, int $decayMinutes = 1): void
    {
        $key = $this->generateKey($request, $action);
        RateLimiter::hit($key, $decayMinutes * 60);
    }

    /**
     * Get remaining attempts
     */
    public function remaining(Request $request, string $action, int $maxAttempts = 60): int
    {
        $key = $this->generateKey($request, $action);
        return RateLimiter::remaining($key, $maxAttempts);
    }

    /**
     * Clear rate limit for user/action
     */
    public function clear(Request $request, string $action): void
    {
        $key = $this->generateKey($request, $action);
        RateLimiter::clear($key);
    }

    /**
     * Get time until rate limit resets
     */
    public function availableIn(Request $request, string $action): int
    {
        $key = $this->generateKey($request, $action);
        return RateLimiter::availableIn($key);
    }

    /**
     * Apply progressive rate limiting (increases with violations)
     */
    public function applyProgressiveLimit(Request $request, string $action): array
    {
        $violationKey = $this->generateKey($request, $action . '_violations');
        $violations = Cache::get($violationKey, 0);
        
        // Progressive limits based on violations
        $limits = $this->getProgressiveLimits($violations);
        
        $key = $this->generateKey($request, $action);
        $isLimited = RateLimiter::tooManyAttempts($key, $limits['max_attempts']);
        
        if ($isLimited) {
            // Increase violation count
            Cache::put($violationKey, $violations + 1, now()->addHours(24));
            
            // Track violation key for admin dashboard
            $this->trackViolationKey($violationKey);
            
            Log::warning('Progressive rate limit applied', [
                'action' => $action,
                'violations' => $violations + 1,
                'limits' => $limits,
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
            ]);
        } else {
            // Hit the rate limiter
            RateLimiter::hit($key, $limits['decay_minutes'] * 60);
        }
        
        return [
            'is_limited' => $isLimited,
            'max_attempts' => $limits['max_attempts'],
            'decay_minutes' => $limits['decay_minutes'],
            'violations' => $violations,
            'remaining' => $isLimited ? 0 : RateLimiter::remaining($key, $limits['max_attempts']),
            'retry_after' => $isLimited ? RateLimiter::availableIn($key) : null,
        ];
    }

    /**
     * Get progressive limits based on violation count
     */
    protected function getProgressiveLimits(int $violations): array
    {
        return match(true) {
            $violations >= 10 => ['max_attempts' => 5, 'decay_minutes' => 60],    // Very strict
            $violations >= 5 => ['max_attempts' => 10, 'decay_minutes' => 30],    // Strict
            $violations >= 2 => ['max_attempts' => 20, 'decay_minutes' => 15],    // Moderate
            default => ['max_attempts' => 60, 'decay_minutes' => 1]               // Normal
        };
    }

    /**
     * Generate unique key for rate limiting
     */
    protected function generateKey(Request $request, string $action): string
    {
        $user = $request->user();
        
        if ($user) {
            return "rate_limit:{$action}:user:{$user->id}";
        }
        
        return "rate_limit:{$action}:ip:{$request->ip()}";
    }

    /**
     * Get rate limit statistics
     */
    public function getStatistics(Request $request, array $actions): array
    {
        $stats = [];
        
        foreach ($actions as $action => $limits) {
            $key = $this->generateKey($request, $action);
            $stats[$action] = [
                'max_attempts' => $limits['max_attempts'],
                'remaining' => RateLimiter::remaining($key, $limits['max_attempts']),
                'reset_time' => now()->addSeconds(RateLimiter::availableIn($key))->toISOString(),
                'is_limited' => RateLimiter::tooManyAttempts($key, $limits['max_attempts']),
            ];
        }
        
        return $stats;
    }

    /**
     * Whitelist IP addresses (bypass rate limiting)
     */
    public function isWhitelisted(string $ip): bool
    {
        $whitelist = config('rate-limit.whitelist', [
            '127.0.0.1',
            '::1',
        ]);
        
        return in_array($ip, $whitelist);
    }

    /**
     * Blacklist IP addresses (always rate limited)
     */
    public function isBlacklisted(string $ip): bool
    {
        $blacklist = Cache::get('rate_limit_blacklist', []);
        return in_array($ip, $blacklist);
    }

    /**
     * Add IP to temporary blacklist
     */
    public function addToBlacklist(string $ip, int $minutes = 60): void
    {
        $blacklist = Cache::get('rate_limit_blacklist', []);
        $blacklist[] = $ip;
        
        Cache::put('rate_limit_blacklist', array_unique($blacklist), now()->addMinutes($minutes));
        
        Log::warning('IP added to rate limit blacklist', [
            'ip' => $ip,
            'duration_minutes' => $minutes,
        ]);
    }
    
    /**
     * Track violation key for admin dashboard
     */
    private function trackViolationKey(string $violationKey): void
    {
        $violationKeys = Cache::get('rate_limit_violation_keys', []);
        
        if (!in_array($violationKey, $violationKeys)) {
            $violationKeys[] = $violationKey;
            Cache::put('rate_limit_violation_keys', $violationKeys, now()->addHours(48));
        }
    }
    
    /**
     * Clean up expired violation keys
     */
    public function cleanupExpiredViolations(): void
    {
        $violationKeys = Cache::get('rate_limit_violation_keys', []);
        $activeKeys = [];
        
        foreach ($violationKeys as $key) {
            if (Cache::has($key)) {
                $activeKeys[] = $key;
            }
        }
        
        Cache::put('rate_limit_violation_keys', $activeKeys, now()->addHours(48));
    }
}
