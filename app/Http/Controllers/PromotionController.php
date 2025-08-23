<?php
namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    // List active promotions
    public function index()
    {
        $promotions = Promotion::where('status', true)
                               ->where('start_date', '<=', now())
                               ->where('end_date', '>=', now())
                               ->get();
        return response()->json($promotions);
    }

    // Create promotion
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:discount,bogo,free_shipping',
            'value' => 'nullable|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'applicable_products' => 'nullable|array',
            'status' => 'boolean'
        ]);

        $promotion = Promotion::create($data);
        return response()->json($promotion);
    }

    // Update promotion
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update($request->all());
        return response()->json($promotion);
    }

    // Delete promotion
    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Promotion deleted']);
    }
//     public function activePromotions()
// {
//     $promotions = Promotion::where('status', 1)->get(); // only active
//     return response()->json($promotions);
// }
public function activePromotions()
{
    $today = now()->toDateString();

    $promotions = Promotion::where('status', true)
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->with('products') // eager load products
        ->get();

    return response()->json($promotions);
}

}
