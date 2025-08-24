<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
   public function addToCart(Request $request)
{
    $validated = $request->validate([
        'customer_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'color' => 'nullable|string',
        'size' => 'nullable|string',
    ]);

    // Check if already in cart, update quantity
    $cartItem = Cart::where('customer_id', $request->customer_id)
        ->where('product_id', $request->product_id)
        ->first();

    if ($cartItem) {
        $cartItem->quantity += $request->quantity;
        $cartItem->save();
    } else {
        Cart::create($validated);
    }

    return response()->json(['message' => 'Added to cart successfully']);
}

public function index($customer_id)
{
    $cartItems = Cart::where('customer_id', $customer_id)
        ->with('product') // eager load product details
        ->get();

    return response()->json($cartItems);
}

public function update(Request $request, $id)
{
    $cart = Cart::findOrFail($id);
    $cart->quantity = $request->quantity;
    $cart->save();

    return response()->json(['message' => 'Quantity updated', 'cart' => $cart]);
}

public function getCartItems($userId)
{
    $cartItems = Cart::with('product')
        ->where('customer_id', $userId)
        ->get();

    return response()->json($cartItems);
}
public function destroy($id)
{
    $cart = Cart::find($id);

    if (!$cart) {
        return response()->json(['message' => 'Cart item not found'], 404);
    }

    $cart->delete();

    return response()->json(['message' => 'Cart item deleted successfully']);
}

public function getCartCount($customer_id)
{
    $count = Cart::where('customer_id', $customer_id)->count();
    return response()->json(['count' => $count]);
}
}
