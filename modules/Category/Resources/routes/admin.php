<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Controllers\Admin\CategoryController;

Route::group(['middleware' => ['auth:admin']], function () {
    // Soft Delete Routes
    Route::get('/with-trashed', [CategoryController::class, 'indexWithTrashed']);
    Route::get('/only-trashed', [CategoryController::class, 'indexOnlyTrashed']);
    Route::post('/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('/{id}/force', [CategoryController::class, 'forceDelete']);
    
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'delete']);
});
