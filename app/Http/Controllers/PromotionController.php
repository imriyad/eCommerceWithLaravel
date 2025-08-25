<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Promotion;
use Illuminate\Http\Request;
use App\Models\AdminActivity;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    // List all promotions
    public function index()
    {
        $promotions = Promotion::with('products')->get(); // eager load products
        return response()->json($promotions);
    }

    // List active promotions
    public function activePromotions()
    {
        $today = now()->toDateString();

        $promotions = Promotion::where('status', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('products')
            ->get();

        return response()->json($promotions);
    }

    // Create a new promotion
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount,bogo,free_shipping',
            'value' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'applicable_products' => 'nullable|array',
            'status' => 'boolean'
        ]);

        $promotion = Promotion::create($data);
        // if (Auth::check()) {
        //     \Log::info('Authenticated user:', [
        //         'id' => Auth::id(),
        //         'name' => Auth::user()->name,
        //         'email' => Auth::user()->email,
        //         'role' => Auth::user()->role,
        //     ]);
        // }



        // Log admin activity if admin_id is sent
        if (Auth::check() && Auth::user()->role === 'admin') {
            AdminActivity::create([
                'admin_id' => Auth::id(),
                'message' => "Created promotion '{$promotion->name}'"
            ]);
        }

        return response()->json($promotion, 201);
    }

    // Update an existing promotion
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:discount,bogo,free_shipping',
            'value' => 'nullable|numeric',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'applicable_products' => 'nullable|array',
            'status' => 'boolean'
        ]);

        $promotion->update($data);

        // Log admin activity
        if ($request->has('admin_id')) {
            AdminActivity::create([
                'admin_id' => $request->admin_id,
                'message' => "Updated promotion '{$promotion->name}'"
            ]);
        }

        return response()->json($promotion, 200);
    }

    // Delete a promotion
    public function destroy(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotionName = $promotion->name;
        $promotion->delete();

        // Log admin activity
        if ($request->has('admin_id')) {
            AdminActivity::create([
                'admin_id' => $request->admin_id,
                'message' => "Deleted promotion '{$promotionName}'"
            ]);
        }

        return response()->json(['message' => 'Promotion deleted'], 200);
    }

    // Show a single promotion
    public function show($id)
    {
        $promotion = Promotion::with('products')->findOrFail($id);
        return response()->json($promotion);
    }
}
