<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomRateLimitMiddleware;
use Modules\Product\Controllers\Admin\ProductController;

Route::group(['middleware' => ['auth:admin', CustomRateLimitMiddleware::class . ':admin']], function () {
    // Product listing with higher limits
    Route::get('/', [ProductController::class, 'index'])
        ->middleware(CustomRateLimitMiddleware::class . ':products');
    
    // Product creation
    Route::post('/', [ProductController::class, 'store'])
        ->middleware(CustomRateLimitMiddleware::class . ':api');
    
    // Product details
    Route::get('/{id}', [ProductController::class, 'show'])
        ->middleware(CustomRateLimitMiddleware::class . ':products');
    
    // Product updates
    Route::put('/{id}', [ProductController::class, 'update'])
        ->middleware(CustomRateLimitMiddleware::class . ':api');
    
    // Product deletion
    Route::delete('/{id}', [ProductController::class, 'delete'])
        ->middleware(CustomRateLimitMiddleware::class . ':api');
});
