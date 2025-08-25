<?php

namespace App\Http\Controllers;

use App\Models\SellerActivity;
use Illuminate\Http\Request;

class SellerActivityController extends Controller
{
    
    public function index($id)
    {
        // Fetch recent activities for a specific admin, latest first
        $activities = SellerActivity::where('seller_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10) // limit to latest 10 activities
            ->get();

        return response()->json($activities, 200);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
        ]);

        $activity = SellerActivity::create($data);

        return response()->json($activity, 201);
    }
}

