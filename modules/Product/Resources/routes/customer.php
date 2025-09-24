<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CustomRateLimitMiddleware;
use Modules\Product\Controllers\Admin\ProductController;

Route::group(['middleware' => [CustomRateLimitMiddleware::class . ':customer']], function () {
    // Product listing with higher limits
    Route::get('/', [ProductController::class, 'index'])
        ->middleware(CustomRateLimitMiddleware::class . ':products');
    // Product details
    Route::get('/{id}', [ProductController::class, 'show'])
        ->middleware(CustomRateLimitMiddleware::class . ':products');
});
