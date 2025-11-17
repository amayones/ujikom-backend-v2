<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat']);
        
        // Customer hanya lihat order sendiri
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        }
        // Admin, owner lihat semua
        
        return response()->json([
            'success' => true,
            'data' => $query->orderBy('created_at', 'desc')->get()
        ]);
    }
    
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $query = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat']);
        
        // Customer hanya bisa lihat order sendiri
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        }
        
        $order = $query->find($id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $order]);
    }
}
