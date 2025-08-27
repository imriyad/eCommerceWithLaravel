<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $userId, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review added successfully',
            'review' => $review
        ], 201);
    }
     public function getProductReviews($product_id)
    {
        // Get all reviews for the product
        $reviews = Review::where('product_id', $product_id)->get();

        // Calculate average rating
        $avgRating = $reviews->avg('rating'); // Laravel collection helper

        return response()->json([
            'reviews' => $reviews,
            'avg_rating' => round($avgRating, 1), // optional rounding
        ]);
    }
}
