<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {

    Route::get('/user', [\App\Http\Controllers\AuthController::class, 'user']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    Route::apiResource('/products', \App\Http\Controllers\ProductController::class);
});

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
