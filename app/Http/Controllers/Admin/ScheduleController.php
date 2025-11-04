<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $schedules = Schedule::with(['film', 'studio'])->get();
        return $this->successResponse($schedules, 'Schedules retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'show_time' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Check for schedule conflicts (studio double booking)
        $showTime = \Carbon\Carbon::parse($request->show_time);
        $film = \App\Models\Film::find($request->film_id);
        $filmDuration = $film->duration; // in minutes
        
        $conflictingSchedule = Schedule::where('studio_id', $request->studio_id)
            ->where(function($query) use ($showTime, $filmDuration) {
                $endTime = $showTime->copy()->addMinutes($filmDuration + 30); // +30 min buffer
                $query->whereBetween('show_time', [$showTime->copy()->subMinutes($filmDuration + 30), $showTime])
                      ->orWhereBetween('show_time', [$showTime, $endTime]);
            })
            ->exists();

        if ($conflictingSchedule) {
            return $this->errorResponse('Studio sudah memiliki jadwal pada waktu tersebut', 422);
        }

        $schedule = Schedule::create($request->all());
        $schedule->load(['film', 'studio']);
        
        return $this->successResponse($schedule, 'Schedule created successfully', 201);
    }

    public function show($id)
    {
        $schedule = Schedule::with(['film', 'studio'])->find($id);
        
        if (!$schedule) {
            return $this->errorResponse('Schedule not found', 404);
        }

        return $this->successResponse($schedule, 'Schedule retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        
        if (!$schedule) {
            return $this->errorResponse('Schedule not found', 404);
        }

        $validator = Validator::make($request->all(), [
            'film_id' => 'required|exists:films,id',
            'studio_id' => 'required|exists:studios,id',
            'show_time' => 'required|date|after:now'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        // Check for schedule conflicts (exclude current schedule)
        $showTime = \Carbon\Carbon::parse($request->show_time);
        $film = \App\Models\Film::find($request->film_id);
        $filmDuration = $film->duration;
        
        $conflictingSchedule = Schedule::where('studio_id', $request->studio_id)
            ->where('id', '!=', $id)
            ->where(function($query) use ($showTime, $filmDuration) {
                $endTime = $showTime->copy()->addMinutes($filmDuration + 30);
                $query->whereBetween('show_time', [$showTime->copy()->subMinutes($filmDuration + 30), $showTime])
                      ->orWhereBetween('show_time', [$showTime, $endTime]);
            })
            ->exists();

        if ($conflictingSchedule) {
            return $this->errorResponse('Studio sudah memiliki jadwal pada waktu tersebut', 422);
        }

        $schedule->update($request->all());
        $schedule->load(['film', 'studio']);
        
        return $this->successResponse($schedule, 'Schedule updated successfully');
    }

    public function destroy($id)
    {
        $schedule = Schedule::find($id);
        
        if (!$schedule) {
            return $this->errorResponse('Schedule not found', 404);
        }

        $schedule->delete();
        return $this->successResponse(null, 'Schedule deleted successfully');
    }
}