<?php

use Illuminate\Support\Facades\Route;
use Modules\RateLimit\Controllers\RateLimitController;

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('statistics', [RateLimitController::class, 'statistics']);
    Route::post('clear-user-limits', [RateLimitController::class, 'clearUserLimits']);
    Route::post('blacklist-ip', [RateLimitController::class, 'blacklistIp']);
    Route::delete('blacklist-ip', [RateLimitController::class, 'removeFromBlacklist']);
    Route::put('configuration', [RateLimitController::class, 'updateConfiguration']);
});
