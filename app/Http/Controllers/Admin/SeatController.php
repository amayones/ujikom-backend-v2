<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $seats = Seat::with('studio')->get();
        return $this->successResponse($seats, 'Seats retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'studio_id' => 'required|exists:studios,id',
            'row' => 'required|string|max:5',
            'column' => 'required|integer|min:1',
            'category' => 'required|in:regular,vip',
            'status' => 'required|in:available,maintenance'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $seat = Seat::create($request->all());
        $seat->load('studio');
        
        return $this->successResponse($seat, 'Seat created successfully', 201);
    }

    public function show($id)
    {
        $seat = Seat::with('studio')->find($id);
        
        if (!$seat) {
            return $this->errorResponse('Seat not found', 404);
        }

        return $this->successResponse($seat, 'Seat retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $seat = Seat::find($id);
        
        if (!$seat) {
            return $this->errorResponse('Seat not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'studio_id' => 'required|exists:studios,id',
            'row' => 'required|string|max:5',
            'column' => 'required|integer|min:1',
            'category' => 'required|in:regular,vip',
            'status' => 'required|in:available,maintenance'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $seat->update($request->all());
        $seat->load('studio');
        
        return $this->successResponse($seat, 'Seat updated successfully');
    }

    public function destroy($id)
    {
        $seat = Seat::find($id);
        
        if (!$seat) {
            return $this->errorResponse('Seat not found', 404);
        }

        $seat->delete();
        return $this->successResponse(null, 'Seat deleted successfully');
    }
}