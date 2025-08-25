<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Get logged-in user's wishlist
    public function index()
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();
        return response()->json($wishlist);
    }

    // Add product to wishlist
    public function store($user_id, $product_id)
    {
        // validate that product exists
        $product = Product::findOrFail($product_id);

        // validate that user exists (optional but good practice)
        $user = User::findOrFail($user_id);

        // create or get wishlist entry
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Product added to wishlist successfully!',
            'wishlist' => $wishlist
        ], 201);
    }


    // Remove product from wishlist
    public function destroy($user_id, $product_id)
    {
        $wishlist = Wishlist::where('user_id', $user_id)
                            ->where('product_id', $product_id)
                            ->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Product not found in wishlist'], 404);
        }

        $wishlist->delete();

        return response()->json(['message' => 'Product removed from wishlist']);
    }

    public function getUserWishlist($user_id)
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', $user_id)
            ->get();
        return response()->json($wishlist);
    }
}
