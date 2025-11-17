<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $discounts
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:discounts,code|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = Discount::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Discount created successfully',
            'data' => $discount
        ], 201);
    }

    public function show($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $discount
        ]);
    }

    public function update(Request $request, $id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:discounts,code,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $discount->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully',
            'data' => $discount
        ]);
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Discount not found'
            ], 404);
        }

        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Discount deleted successfully'
        ]);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Code is required'
            ], 422);
        }

        $discount = Discount::where('code', $request->code)->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid discount code'
            ], 404);
        }

        if (!$discount->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Discount code is not valid or has expired'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Discount code is valid',
            'data' => $discount
        ]);
    }
}
