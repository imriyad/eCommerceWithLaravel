<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\AdminActivityController;
use App\Http\Controllers\SellerActivityController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
});

Route::middleware(['auth'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', function () {
    $users = User::all();
    return response()->json($users);
});



Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome Admin']);
    });
});
Route::get('/admin/stats', [AdminStatsController::class, 'index']);
Route::get('/admin/users', [AdminUserController::class, 'index']);
Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy']);
Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);
Route::post('/admin/users', [AdminUserController::class, 'store']);
Route::get('/admin/recent-activities/{id}', [AdminActivityController::class, 'index']);
Route::post('/admin/recent-activities', [AdminActivityController::class, 'store']);

Route::middleware(['auth', 'role:seller'])->group(function () {
    Route::get('/seller/dashboard', function () {
        return response()->json(['message' => 'Welcome Seller']);
    });
});
Route::get('/seller/stats', [\App\Http\Controllers\SellerStatsController::class, 'index']);
Route::get('/seller/recent-activities/{id}', [SellerActivityController::class, 'index']);
Route::post('/seller/recent-activities', [SellerActivityController::class, 'store']);

Route::get('customer/dashboard/{id}', [CustomerController::class, 'dashboard']);
Route::get('/customer/profile/{id}', [\App\Http\Controllers\CustomerController::class, 'profile']);


// routes/api.php

  Route::post('/customer/profile/{id}', [CustomerController::class, 'updateProfile']);
Route::post('/customer/change-password/{id}', [CustomerController::class, 'changePassword']);



// Route::put('/admin/users/{id}', [AdminUserController::class, 'update']);

Route::get('customer/orders/{id}', [CustomerController::class, 'orders']);
Route::get('customer/recommended/{id}', [CustomerController::class, 'recommendedProducts']);


// Public routes for guests, no auth middleware required
Route::get('/public-info', function () {
    return response()->json(['message' => 'Welcome Guest']);
});

// Product Routes
Route::get('/products', [ProductController::class, 'index']);       // List all products
Route::get('/products/{id}', [ProductController::class, 'show']);   // Show single product
Route::post('/products', [ProductController::class, 'store']);      // Create new product
Route::put('/products/{id}', [ProductController::class, 'update']); // Update product
Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete product
Route::get('/categories/{id}/products', [ProductController::class, 'byCategory']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);

Route::post('/cart', [CartController::class, 'addToCart']);
Route::get('/cart/{customer_id}', [CartController::class, 'index']);
Route::get('/cart/{userId}', [CartController::class, 'getCartItems']);
Route::delete('/cart/{id}', [CartController::class, 'destroy']);
Route::put('/cart/{id}', [CartController::class, 'update']);
Route::get('/cart', [CartController::class, 'index']);
Route::get('/cart/{customer_id}/count', [CartController::class, 'getCartCount']);

Route::get('/wishlist', [WishlistController::class, 'index']);
Route::post('/wishlist/{user_id}/{product_id}', [WishlistController::class, 'store']);
Route::delete('/wishlist/{user_id}/{product_id}', [WishlistController::class, 'destroy']);
Route::get('/wishlist/{user_id}', [WishlistController::class, 'getUserWishlist']);

Route::get('/addresses', [AddressController::class, 'index']);
Route::post('/addresses', [AddressController::class, 'store']);
Route::put('/addresses/{id}', [AddressController::class, 'update']);
Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);
Route::get('/orders', [OrderController::class, 'index']);           // List orders with optional search

Route::patch('/orders/{order}', [OrderController::class, 'update']); // Update order status
Route::delete('/orders/{order}', [OrderController::class, 'destroy']); // Cancel order

Route::post('/orders/buy-now', [OrderController::class, 'buyNow']);
Route::post('/orders/confirm', [OrderController::class, 'confirmOrder']); // webhook or manual
Route::get('/customer/orders', [OrderController::class, 'getUserOrders']);


Route::get('/promotions', [PromotionController::class, 'index']);
Route::post('/promotions', [PromotionController::class, 'store']);
Route::put('/promotions/{id}', [PromotionController::class, 'update']);
Route::delete('/promotions/{id}', [PromotionController::class, 'destroy']);
Route::get('/promotions/active', [PromotionController::class, 'activePromotions']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('auth')
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
    
Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);
Route::post('/reviews/{userId}/{productId}', [ReviewController::class, 'store']);
Route::get('/reviews/product/{product_id}', [ReviewController::class, 'getProductReviews']);

Route::get('/reviews/user/{user_id}', [ReviewController::class, 'getUserReviews']);
Route::get('/reviews', [ReviewController::class, 'index']);
Route::put('/reviews/{id}', [ReviewController::class, 'update']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

Route::post('/ai/chat', [ChatController::class, 'chat']);
Route::get('/ai/chat/{sessionId}', [ChatController::class, 'getChatHistory']);

Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::put('/profile/{id}', [ProfileController::class, 'update']);
Route::post('/profile/{id}/upload-image', [ProfileController::class, 'uploadImage']);