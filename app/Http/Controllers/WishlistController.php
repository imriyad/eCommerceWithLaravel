<?php
// app/Http/Controllers/WishlistController.php
namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Get wishlist items for the authenticated user
    public function index()
    {
        $user = Auth::user();
        $wishlist = Wishlist::with('product')->where('user_id', $user->id)->get();

        return response()->json($wishlist);
    }

    // Add product to wishlist
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();

        // Prevent duplicates
        $exists = Wishlist::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
        if ($exists) {
            return response()->json(['message' => 'Product already in wishlist'], 409);
        }

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['message' => 'Added to wishlist', 'wishlist' => $wishlist], 201);
    }

    // Remove product from wishlist
    public function destroy($id)
    {
        $user = Auth::user();

        $wishlist = Wishlist::where('user_id', $user->id)->where('product_id', $id)->first();

        if (!$wishlist) {
            return response()->json(['message' => 'Item not found in wishlist'], 404);
        }

        $wishlist->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
