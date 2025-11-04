<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Traits\ApiResponseTrait;

class FilmController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $playNow = Film::where('status', 'play_now')->get();
        $comingSoon = Film::where('status', 'coming_soon')->get();
        
        return $this->successResponse([
            'play_now' => $playNow,
            'coming_soon' => $comingSoon
        ], 'Films retrieved successfully');
    }

    public function show($id)
    {
        $film = Film::with(['schedules' => function($query) {
            $query->where('show_time', '>', now())
                  ->orderBy('show_time')
                  ->with('studio');
        }])->find($id);
        
        if (!$film) {
            return $this->errorResponse('Film not found', 404);
        }

        return $this->successResponse($film, 'Film detail retrieved successfully');
    }
}