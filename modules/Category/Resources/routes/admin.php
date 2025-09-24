<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Controllers\Admin\CategoryController;

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'delete']);
});
