<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CustomRateLimitMiddleware
{
    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): SymfonyResponse
    {
        $key = $this->resolveRequestSignature($request, $limiter);
        
        // Check if rate limit is exceeded
        if (RateLimiter::tooManyAttempts($key, $this->getMaxAttempts($limiter))) {
            $this->logRateLimitExceeded($request, $key, $limiter);
            return $this->buildRateLimitResponse($key, $limiter);
        }

        // Hit the rate limiter
        RateLimiter::hit($key, $this->getDecayMinutes($limiter) * 60);

        $response = $next($request);

        // Add rate limit headers
        return $this->addRateLimitHeaders($response, $key, $limiter);
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request, string $limiter): string
    {
        $user = $request->user();
        
        if ($user) {
            // For authenticated users, use user ID
            return "rate_limit:{$limiter}:user:{$user->id}";
        }
        
        // For guests, use IP address
        return "rate_limit:{$limiter}:ip:{$request->ip()}";
    }

    /**
     * Get max attempts for limiter
     */
    protected function getMaxAttempts(string $limiter): int
    {
        return match($limiter) {
            'api' => 60,           // 60 requests per minute
            'auth' => 5,           // 5 login attempts per minute
            'orders' => 10,        // 10 order operations per minute
            'products' => 100,     // 100 product requests per minute
            'admin' => 200,        // 200 admin requests per minute
            'guest' => 30,         // 30 requests per minute for guests
            default => 60
        };
    }

    /**
     * Get decay minutes for limiter
     */
    protected function getDecayMinutes(string $limiter): int
    {
        return match($limiter) {
            'auth' => 5,           // 5 minutes for auth
            'orders' => 1,         // 1 minute for orders
            'products' => 1,       // 1 minute for products
            'admin' => 1,          // 1 minute for admin
            'guest' => 1,          // 1 minute for guests
            default => 1
        };
    }

    /**
     * Build rate limit exceeded response
     */
    protected function buildRateLimitResponse(string $key, string $limiter): JsonResponse
    {
        $retryAfter = RateLimiter::availableIn($key);
        
        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.',
            'error_code' => 'RATE_LIMIT_EXCEEDED',
            'retry_after' => $retryAfter,
            'limiter' => $limiter,
        ], 429)->withHeaders([
            'Retry-After' => $retryAfter,
            'X-RateLimit-Limit' => $this->getMaxAttempts($limiter),
            'X-RateLimit-Remaining' => 0,
        ]);
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders($response, string $key, string $limiter)
    {
        $maxAttempts = $this->getMaxAttempts($limiter);
        $remainingAttempts = RateLimiter::remaining($key, $maxAttempts);
        $retryAfter = RateLimiter::availableIn($key);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $remainingAttempts),
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }

    /**
     * Log rate limit exceeded
     */
    protected function logRateLimitExceeded(Request $request, string $key, string $limiter): void
    {
        Log::warning('Rate limit exceeded', [
            'key' => $key,
            'limiter' => $limiter,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
        ]);
    }
}
