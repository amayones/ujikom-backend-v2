<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Traits\ApiResponseTrait;

class ScheduleController extends Controller
{
    use ApiResponseTrait;

    public function getByFilm($filmId)
    {
        $schedules = Schedule::with(['studio', 'film'])
            ->where('film_id', $filmId)
            ->where('show_time', '>', now())
            ->orderBy('show_time')
            ->get()
            ->map(function($schedule) {
                // Check if weekend or weekday
                $isWeekend = \Carbon\Carbon::parse($schedule->show_time)->isWeekend();
                $dayType = $isWeekend ? 'weekend' : 'weekday';
                
                // Get base price from film
                $schedule->base_price = $schedule->film->base_price;
                $schedule->day_type = $dayType;
                
                return $schedule;
            });

        return $this->successResponse($schedules, 'Schedules retrieved successfully');
    }
}