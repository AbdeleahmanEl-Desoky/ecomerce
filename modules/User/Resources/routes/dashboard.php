<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Controllers\Admin\UserController;

Route::middleware(['auth:admin'])->group(function () {
    // Soft Delete Routes
    Route::get('/with-trashed', [UserController::class, 'indexWithTrashed']);
    Route::get('/only-trashed', [UserController::class, 'indexOnlyTrashed']);
    Route::post('/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/{id}/force', [UserController::class, 'forceDelete']);

    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'delete']);
    
});
