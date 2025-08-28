<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AdminActivity; 
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        if (Auth::check()) {
            AdminActivity::create([
                'admin_id' => Auth::id(),
                'message' => "Added new product: {$product->name}",
            ]);
        }

        return response()->json($product, 201);
    }

    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    // public function index()
    // {
    //     $products = Product::withCount('reviews')
    //         ->withAvg('reviews', 'rating')
    //         ->get(); // paginate 10 items per page

    //     // Ensure default values
    //     $products->getCollection()->transform(function ($product) {
    //         $product->reviews_count = $product->reviews_count ?? 0;
    //         $product->reviews_avg_rating = $product->reviews_avg_rating ?? 0;
    //         return $product;
    //     });

    //     // Return paginated response with metadata
    //     return response()->json([
    //         'data' => $products->items(),        // actual products
    //         'current_page' => $products->currentPage(),
    //         'last_page' => $products->lastPage(),
    //         'per_page' => $products->perPage(),
    //         'total' => $products->total(),
    //     ], 200);
    // }



    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string', // handle file if needed
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        // Log admin activity
        if (Auth::check()) {
            AdminActivity::create([
                'admin_id' => Auth::id(),
                'message' => "Updated product: {$product->name}",
            ]);
        }

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name; // store name before deletion
        $product->delete();

        // Log admin activity
        if (Auth::check()) {
            AdminActivity::create([
                'admin_id' => Auth::id(),
                'message' => "Deleted product: {$productName}",
            ]);
        }

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }

    public function byCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();
        return response()->json($products);
    }
}
