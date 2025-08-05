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

}
