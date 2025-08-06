<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

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

    // Handle file upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images', 'public');
        $data['image'] = $path;
    }

    $product = Product::create($data);

    return response()->json($product, 201);
}
// public function index()
// {
//     return response()->json(Product::paginate(6)); 
// }
public function index()
{
    return response()->json(Product::all(), 200);
}
public function show($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json($product);
}

 // Update product by ID
    public function update(Request $request, $id)
    {
        // Validate request data (adjust rules as needed)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string', // or handle file upload if needed
        ]);

        // Find product or fail
        $product = Product::findOrFail($id);

        // Update product fields
        $product->update($validated);

        // Return updated product with 200 status
        return response()->json($product, 200);
    }

    // Delete product by ID
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete product
        $product->delete();

        // Return success message
        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }



}
