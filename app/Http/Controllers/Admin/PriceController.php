<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Price;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriceController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $prices = Price::all();
        return $this->successResponse($prices, 'Prices retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_type' => 'required|in:weekday,weekend',
            'seat_category' => 'required|in:regular,vip',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $price = Price::create($request->all());
        return $this->successResponse($price, 'Price created successfully', 201);
    }

    public function show($id)
    {
        $price = Price::find($id);
        
        if (!$price) {
            return $this->errorResponse('Price not found', 404);
        }

        return $this->successResponse($price, 'Price retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $price = Price::find($id);
        
        if (!$price) {
            return $this->errorResponse('Price not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'day_type' => 'required|in:weekday,weekend',
            'seat_category' => 'required|in:regular,vip',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $price->update($request->all());
        return $this->successResponse($price, 'Price updated successfully');
    }

    public function destroy($id)
    {
        $price = Price::find($id);
        
        if (!$price) {
            return $this->errorResponse('Price not found', 404);
        }

        $price->delete();
        return $this->successResponse(null, 'Price deleted successfully');
    }
}