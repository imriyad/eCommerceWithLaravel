<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index()  {
        $totalProducts=Product::count();
        $totalUsers=User::count();
        $totalOrders=Order::count();
        $totalOrders = Order::whereIn('status', ['pending', 'processing'])->count();


        return response()->json([
            'total_products'=>$totalProducts,
            'total_users'=>$totalUsers,
            'total_orders'=>$totalOrders,
            
        ]);
    }

}
