<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\ProductController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin auth
Route::post('/admin/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/user', [AdminAuthController::class, 'user']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::apiResource('/products', ProductController::class);
});

// Customer auth (public)
Route::prefix('customer')->group(function () {
    Route::post('/register', [Customer::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/auth/google/redirect', [CustomerAuthController::class, 'googleRedirect']);
    Route::get('/auth/google/callback', [CustomerAuthController::class, 'googleCallback']);
});

// Customer protected
Route::middleware(['auth:sanctum', 'isCustomer'])->prefix('customer')->group(function () {
    Route::get('/user', [CustomerAuthController::class, 'user']);
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
});
