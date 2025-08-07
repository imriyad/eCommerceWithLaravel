<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', function () {
    $users=User::all();
    return response()->json($users);
});


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome Admin']);
    });
});

Route::middleware(['auth:sanctum', 'role:seller'])->group(function () {
    Route::get('/seller/dashboard', function () {
        return response()->json(['message' => 'Welcome Seller']);
    });
});

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {
    Route::get('/customer/dashboard', function () {
        return response()->json(['message' => 'Welcome Customer']);
    });
});

// Public routes for guests, no auth middleware required
Route::get('/public-info', function () {
    return response()->json(['message' => 'Welcome Guest']);
});

//Route::middleware('auth:sanctum')->post('/products', [ProductController::class, 'store']);

// Product Routes
Route::get('/products', [ProductController::class, 'index']);       // List all products
Route::get('/products/{id}', [ProductController::class, 'show']);   // Show single product
Route::post('/products', [ProductController::class, 'store']);      // Create new product
Route::put('/products/{id}', [ProductController::class, 'update']); // Update product
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete product


Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

Route::post('/cart', [CartController::class, 'addToCart']);
Route::get('/cart/{customer_id}', [CartController::class, 'index']);
Route::get('/cart/{userId}', [CartController::class, 'getCartItems']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);
