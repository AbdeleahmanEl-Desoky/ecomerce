<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Controllers\Admin\OrderController;
use App\Http\Middleware\CustomRateLimitMiddleware;

Route::group(['middleware' => ['auth:admin', CustomRateLimitMiddleware::class . ':admin']], function () {
    // Order listing with higher limits for admins
    Route::get('/', [OrderController::class, 'index']);
    
    // Order creation with specific rate limiting
    Route::post('/', [OrderController::class, 'store'])
        ->middleware(CustomRateLimitMiddleware::class . ':orders');
    
    // Stock status with moderate limiting
    Route::get('/stock-status', [OrderController::class, 'stockStatus'])
        ->middleware(CustomRateLimitMiddleware::class . ':api');
    
    // Order details
    Route::get('/{id}', [OrderController::class, 'show']);
    
    // Order updates with specific limiting
    Route::put('/{id}', [OrderController::class, 'update'])
        ->middleware(CustomRateLimitMiddleware::class . ':orders');
    
    // Order cancellation with strict limiting
    Route::patch('/{id}/cancel', [OrderController::class, 'cancel'])
        ->middleware(CustomRateLimitMiddleware::class . ':orders');
    
    // Order deletion with strict limiting
    Route::delete('/{id}', [OrderController::class, 'delete'])
        ->middleware(CustomRateLimitMiddleware::class . ':orders');
});
