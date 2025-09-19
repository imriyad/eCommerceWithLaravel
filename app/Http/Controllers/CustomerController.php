<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
        $loyaltyPoints = 0; // Implement based on your loyalty model

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

    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }
    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update name
        $user->name = $validated['name'];

        // Handle profile picture upload (if exists)
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile_pictures'), $fileName);
            $user->profile_picture = 'uploads/profile_pictures/' . $fileName;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'customer' => $user
        ]);
    }
}
