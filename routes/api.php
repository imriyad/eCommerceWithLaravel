<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminStatsController;
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
    // Route::get('/admin/stats', [AdminStatsController::class, 'index']);

});
Route::get('admin/stats',[AdminStatsController::class,'index']);
Route::get('admin/users',[AdminUserController::class,'index']);
Route::delete('admin/users/{id}',[AdminUserController::class,'destroy']);
Route::put('admin/users/{id}',[AdminUserController::class, 'update']);
Route::post('admin/users',[AdminUserController::class,'store']);


// Route::post('/users', [AdminUserController::class, 'store']);  // Create
// Route::get('/users/{id}', [AdminUserController::class, 'show']); // Read single
// Route::put('/users/{id}', [AdminUserController::class, 'update']); // Update
// Route::delete('/users/{id}', [AdminUserController::class, 'destroy']); // Delete


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
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::get('/cart', [CartController::class, 'index']);



Route::get('/wishlist', [WishlistController::class, 'index']);
Route::post('/wishlist', [WishlistController::class, 'store']);
Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);


Route::get('/addresses', [AddressController::class, 'index']);
Route::post('/addresses', [AddressController::class, 'store']);
Route::put('/addresses/{id}', [AddressController::class, 'update']);
Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);


Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);


Route::get('/orders', [OrderController::class, 'index']);           // List orders with optional search




Route::patch('/orders/{order}', [OrderController::class, 'update']); // Update order status
Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Cancel order

// Route::get('/test-image', function () {
//     $imageDir = public_path('storage/images');
//     if (!file_exists($imageDir)) {
//         mkdir($imageDir, 0777, true);
//     }

//     $faker = \Faker\Factory::create();

//     try {
//         $fileName = $faker->image($imageDir, 640, 480, 'product', false);
//     } catch (\Exception $e) {
//         return "Error creating image: " . $e->getMessage();
//     }

//     return "Image created: " . $fileName;
// });