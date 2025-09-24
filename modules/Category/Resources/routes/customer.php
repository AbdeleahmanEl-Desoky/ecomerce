<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Controllers\Customer\CategoryController;

Route::get('/', [CategoryController::class, 'index']);
Route::get('/{id}', [CategoryController::class, 'show']);

