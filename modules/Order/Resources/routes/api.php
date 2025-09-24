<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Controllers\OrderController;

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'delete']);
});
