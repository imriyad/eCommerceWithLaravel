<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Get current user's wishlist items
    public function index()
    {
        $user = Auth::user();

        $wishlist = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($wishlist);
    }

    // Add a product to wishlist
    public function store(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $wishlist = Wishlist::firstOrCreate([
        'user_id' => $user->id,
        'product_id' => $validated['product_id'],
    ]);

    return response()->json(['message' => 'Added to wishlist', 'data' => $wishlist]);
}


    // Remove product from wishlist
    public function destroy($productId)
    {
        $user = Auth::user();

        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        if ($deleted) {
            return response()->json(['message' => 'Product removed from wishlist']);
        }

        return response()->json(['message' => 'Product not found in wishlist'], 404);
    }
}
