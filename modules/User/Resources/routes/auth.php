<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Controllers\Admin\AuthAdminController;
use Modules\User\Controllers\Customer\AuthCustomerController;
use App\Http\Middleware\CustomRateLimitMiddleware;

// Authentication routes with strict rate limiting
Route::post('admin/login', [AuthAdminController::class, 'login'])
    ->middleware(CustomRateLimitMiddleware::class . ':auth');
    
Route::post('customer/login', [AuthCustomerController::class, 'login'])
    ->middleware(CustomRateLimitMiddleware::class . ':auth');
    
Route::post('customer/register', [AuthCustomerController::class, 'register'])
    ->middleware(CustomRateLimitMiddleware::class . ':auth');

// Admin-specific protected routes
Route::prefix('admin')->middleware(['auth:admin', CustomRateLimitMiddleware::class . ':admin'])->group(function () {
    Route::get('profile', [AuthAdminController::class, 'profile']);
    Route::put('profile', [AuthAdminController::class, 'updateProfile']);
    Route::post('logout', [AuthAdminController::class, 'logout']);
});

// Customer-specific protected routes
Route::prefix('customer')->middleware(['auth:customer', CustomRateLimitMiddleware::class . ':api'])->group(function () {
    Route::get('profile', [AuthCustomerController::class, 'profile']);
    Route::put('profile', [AuthCustomerController::class, 'updateProfile']);
    Route::post('logout', [AuthCustomerController::class, 'logout']);
});

