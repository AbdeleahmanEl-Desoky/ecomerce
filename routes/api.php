<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test-error', function () {
    throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Test model not found');
});

Route::get('/test-validation', function (Request $request) {
    $request->validate([
        'required_field' => 'required'
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
