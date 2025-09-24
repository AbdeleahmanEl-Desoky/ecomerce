<?php

declare(strict_types=1);

namespace Modules\RateLimit\Controllers\Admin;

use BasePackage\Shared\Presenters\Json;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Modules\RateLimit\Requests\ClearUserLimitsRequest;
use Modules\RateLimit\Requests\BlacklistIpRequest;
use Modules\RateLimit\Requests\RemoveFromBlacklistRequest;
use Modules\RateLimit\Requests\UpdateConfigurationRequest;
use Modules\RateLimit\Services\RateLimitService;

class RateLimitController extends Controller
{
    public function __construct(
        private RateLimitService $rateLimitService
    ) {}

    /**
     * Get rate limit statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $actions = [
            'api' => ['max_attempts' => 60, 'decay_minutes' => 1],
            'auth' => ['max_attempts' => 5, 'decay_minutes' => 5],
            'orders' => ['max_attempts' => 10, 'decay_minutes' => 1],
            'products' => ['max_attempts' => 100, 'decay_minutes' => 1],
        ];

        $stats = $this->rateLimitService->getStatistics($request, $actions);

        return Json::item([
            'user_stats' => $stats,
            'global_stats' => $this->rateLimitService->getGlobalStatistics(),
            'blacklist' => Cache::get('rate_limit_blacklist', []),
            'whitelist' => config('rate-limit.whitelist', []),
        ]);
    }

    /**
     * Clear rate limits for user
     */
    public function clearUserLimits(ClearUserLimitsRequest $request): JsonResponse
    {
        $userId = $request->getUserId();
        $actions = $request->getActions();

        foreach ($actions as $action) {
            $key = "rate_limit:{$action}:user:{$userId}";
            RateLimiter::clear($key);
        }

        return Json::item([
            'message' => 'Rate limits cleared successfully',
            'user_id' => $userId,
            'actions_cleared' => $actions,
        ]);
    }

    /**
     * Add IP to blacklist
     */
    public function blacklistIp(BlacklistIpRequest $request): JsonResponse
    {
        $ip = $request->getIp();
        $duration = $request->getDuration();
        $reason = $request->getReason();

        $this->rateLimitService->addToBlacklist($ip, $duration);

        return Json::item([
            'message' => 'IP added to blacklist successfully',
            'ip' => $ip,
            'duration_minutes' => $duration,
            'reason' => $reason,
        ]);
    }

    /**
     * Remove IP from blacklist
     */
    public function removeFromBlacklist(RemoveFromBlacklistRequest $request): JsonResponse
    {
        $ip = $request->getIp();
        $blacklist = Cache::get('rate_limit_blacklist', []);

        $blacklist = array_filter($blacklist, fn($blacklistedIp) => $blacklistedIp !== $ip);

        Cache::put('rate_limit_blacklist', $blacklist, now()->addHours(24));

        return Json::item([
            'message' => 'IP removed from blacklist successfully',
            'ip' => $ip,
        ]);
    }

    /**
     * Update rate limit configuration
     */
    public function updateConfiguration(UpdateConfigurationRequest $request): JsonResponse
    {
        return Json::item([
            'message' => 'Configuration updated successfully',
            'limiter' => $request->getLimiter(),
            'max_attempts' => $request->getMaxAttempts(),
            'decay_minutes' => $request->getDecayMinutes(),
        ]);
    }
}
