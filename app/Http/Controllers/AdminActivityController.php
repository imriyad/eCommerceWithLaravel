<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminActivity;

class AdminActivityController extends Controller
{
    public function index($id)
    {
        // Fetch recent activities for a specific admin, latest first
        $activities = AdminActivity::where('admin_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(10) // limit to latest 10 activities
            ->get();

        return response()->json($activities, 200);
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'admin_id' => 'required|exists:users,id',
            'message' => 'required|string|max:255',
        ]);

        $activity = AdminActivity::create($data);

        return response()->json($activity, 201);
    }
}
