<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for rate limiting across the
    | application. You can define different limits for different actions.
    |
    */

    'default_limiter' => 'api',

    'limiters' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
            'description' => 'General API requests'
        ],
        
        'auth' => [
            'max_attempts' => 5,
            'decay_minutes' => 5,
            'description' => 'Authentication attempts'
        ],
        
        'orders' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
            'description' => 'Order operations'
        ],
        
        'products' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
            'description' => 'Product requests'
        ],
        
        'admin' => [
            'max_attempts' => 200,
            'decay_minutes' => 1,
            'description' => 'Admin operations'
        ],
        
        'guest' => [
            'max_attempts' => 30,
            'decay_minutes' => 1,
            'description' => 'Guest user requests'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Progressive Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Enable progressive rate limiting that becomes stricter with violations
    |
    */
    'progressive_limiting' => [
        'enabled' => true,
        'violation_thresholds' => [
            2 => ['max_attempts' => 20, 'decay_minutes' => 15],
            5 => ['max_attempts' => 10, 'decay_minutes' => 30],
            10 => ['max_attempts' => 5, 'decay_minutes' => 60],
        ],
        'violation_reset_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | IP addresses that bypass rate limiting
    |
    */
    'whitelist' => [
        '127.0.0.1',
        '::1',
        // Add your server IPs here
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Additional security configurations
    |
    */
    'security' => [
        'log_violations' => true,
        'auto_blacklist' => [
            'enabled' => true,
            'threshold' => 20, // violations in 1 hour
            'duration_minutes' => 60,
        ],
        'headers' => [
            'include_rate_limit_headers' => true,
            'include_retry_after' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for rate limiting data
    | Compatible with all Laravel cache drivers (file, database, redis, etc.)
    |
    */
    'cache' => [
        'store' => env('RATE_LIMIT_CACHE_STORE', null), // Use default cache driver
        'prefix' => 'rate_limit:',
        'ttl' => 3600, // 1 hour
        'violation_tracking' => [
            'enabled' => true,
            'cleanup_interval' => 24, // hours
        ],
    ],
];
