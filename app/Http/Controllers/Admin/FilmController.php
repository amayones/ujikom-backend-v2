<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FilmController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $films = Film::paginate($perPage);
        return $this->successResponse($films, 'Films retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:play_now,coming_soon',
            'description' => 'required|string',
            'poster' => 'nullable|string',
            'base_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $film = Film::create($request->all());
        return $this->successResponse($film, 'Film created successfully', 201);
    }

    public function show($id)
    {
        $film = Film::find($id);
        
        if (!$film) {
            return $this->errorResponse('Film not found', 404);
        }

        return $this->successResponse($film, 'Film retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $film = Film::find($id);
        
        if (!$film) {
            return $this->errorResponse('Film not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'status' => 'required|in:play_now,coming_soon',
            'description' => 'required|string',
            'poster' => 'nullable|string',
            'base_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        $film->update($request->all());
        return $this->successResponse($film, 'Film updated successfully');
    }

    public function destroy($id)
    {
        $film = Film::find($id);
        
        if (!$film) {
            return $this->errorResponse('Film not found', 404);
        }

        $film->delete();
        return $this->successResponse(null, 'Film deleted successfully');
    }
}