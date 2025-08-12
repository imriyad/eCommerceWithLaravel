<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request input
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'shipping_info.name' => 'required|string|max:255',
            'shipping_info.email' => 'required|email|max:255',
            'shipping_info.address' => 'required|string|max:255',
            'shipping_info.city' => 'required|string|max:255',
            'shipping_info.postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,paypal,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);

        // Start a database transaction for atomicity
        DB::beginTransaction();

        try {
            // Create the Order record
           $order = Order::create([
    'user_id' => $validated['customer_id'], // This should match your DB column 'user_id'
    'order_number' => 'ORD-' . strtoupper(Str::random(10)),
    'status' => 'pending',
    'grand_total' => $validated['total_amount'],
    'item_count' => count($validated['items']),
    'payment_method' => $validated['payment_method'],
    'payment_status' => $validated['payment_method'] === 'cash_on_delivery' ? 'pending' : 'paid',
    'name' => $validated['shipping_info']['name'],
    'email' => $validated['shipping_info']['email'],
    'address' => $validated['shipping_info']['address'],
    'city' => $validated['shipping_info']['city'],
    'postal_code' => $validated['shipping_info']['postal_code'],
]);

            // Create each order item
            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Clear the cart for the customer
            // Cart::where('user_id', $validated['customer_id'])->delete();
            Cart::where('customer_id', $validated['customer_id'])->delete();


            // Commit the transaction on success
            DB::commit();
            Mail::to($order->email)->send(new OrderConfirmationMail($order));

            return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'message' => 'Order placed successfully! Confirmation email sent.'
            ]);

           
        } catch (\Exception $e) {
            // Rollback all DB changes on failure
            DB::rollBack();

            // Log the error for debugging
            Log::error('Order placement failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Order failed: ' . $e->getMessage(),
            ], 500);
        }
        
    }

    public function getOrderDetails($id)
    {
        try {
            // Load order with related items and products
            $order = Order::with('items.product')->findOrFail($id);

            return response()->json([
                'success' => true,
                'order' => $order,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to get order details for ID $id: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details',
            ], 404);
        }
    }
}


