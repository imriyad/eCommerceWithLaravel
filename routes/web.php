<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

// Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin'])->middleware('web');

// Route::middleware('guest')->group(function () {
//     Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin'])->name('login');
// });

// Route::middleware('auth')->group(function () {
//     Route::get('/customer/orders', [OrderController::class, 'getUserOrders']);
// });


// Route::middleware('guest')->group(function () {
//     Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);
// });

// Route::middleware('auth')->group(function () {
//     Route::get('/customer/orders', [OrderController::class, 'getUserOrders']);
// });

// require __DIR__.'/auth.php';
