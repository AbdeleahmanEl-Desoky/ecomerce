<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Controllers\Admin\AuthAdminController;
use Modules\User\Controllers\Customer\AuthCustomerController;

Route::post('admin/login', [AuthAdminController::class, 'login']);
Route::post('customer/login', [AuthCustomerController::class, 'login']);
Route::post('customer/register', [AuthCustomerController::class, 'register']);

// Admin-specific protected routes
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('profile', [AuthAdminController::class, 'profile']);
    Route::put('profile', [AuthAdminController::class, 'updateProfile']);
    Route::post('logout', [AuthAdminController::class, 'logout']);
});

// Customer-specific protected routes
Route::prefix('customer')->middleware(['auth:customer'])->group(function () {
    Route::get('profile', [AuthCustomerController::class, 'profile']);
    Route::put('profile', [AuthCustomerController::class, 'updateProfile']);
    Route::post('logout', [AuthCustomerController::class, 'logout']);
});

