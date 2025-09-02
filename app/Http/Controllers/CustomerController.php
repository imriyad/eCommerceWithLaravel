<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function dashboard($id)
    {
        $user = User::findOrFail($id);

        $stats = $this->getCustomerStats($user->id);
        $recentOrders = $this->getRecentOrders($user->id);
        $recentlyViewed = $this->getRecentlyViewed($user->id);

        return response()->json([
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentlyViewed' => $recentlyViewed
        ]);
    }


    private function getCustomerStats($userId)
    {
        // Total orders
        $totalOrders = Order::where('user_id', $userId)->count();

        // Wishlist items
        $wishlistItems = Wishlist::where('user_id', $userId)->count();

        // Cart items
        $cartItems = Cart::where('customer_id', $userId)->count();

        // Total spent
        $totalSpent = Order::where('user_id', $userId)
            ->whereIn('status', ['delivered', 'shipped'])
            ->sum('grand_total');

        // Reviews (if you have a reviews system)
        $reviews = 0; // Implement based on your reviews model

        // Loyalty points (if you have a loyalty system)
        $loyaltyPoints = 0; 

        return [
            'orders' => $totalOrders,
            'wishlist' => $wishlistItems,
            'cart' => $cartItems,
            'reviews' => $reviews,
            'totalSpent' => $totalSpent,
            'loyaltyPoints' => $loyaltyPoints            

        ];
    }

    private function getRecentOrders($userId)
    {
        $orders = Order::where('user_id', $userId)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'date' => $order->created_at->format('M d, Y'),
                'items' => $order->items->count(),
                'total' => $order->grand_total,
                'status' => ucfirst($order->status),
                'order_number' => $order->order_number
            ];
        });
    }

    private function getRecentlyViewed($userId)
    {
        // This is a placeholder - implement based on your tracking system
        // You might want to create a recently_viewed table or use session storage
        $recentProducts = Product::inRandomOrder()->limit(4)->get();

        return $recentProducts->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image
            ];
        });
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    
    public function orders($id)
{
    $orders = Order::where('user_id', $id)
        ->with('items.product')
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json(['orders' => $orders]);
}

}
