<?php

namespace App\Http\Controllers;

use App\Models\Price;

class PriceController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Prices retrieved successfully',
            'data' => Price::all()
        ]);
    }
}
