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
public function index()
{
    return response()->json(Product::paginate(6)); 
}
public function show($id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json($product);
}



}
