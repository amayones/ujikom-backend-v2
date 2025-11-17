<?php

namespace App\Http\Controllers;

use App\Models\Studio;

class StudioController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Studios retrieved successfully',
            'data' => Studio::all()
        ]);
    }
}
