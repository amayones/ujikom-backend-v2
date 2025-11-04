<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use App\Models\OrderItem;
use App\Traits\ApiResponseTrait;

class SeatController extends Controller
{
    use ApiResponseTrait;

    public function getBySchedule($scheduleId)
    {
        $schedule = Schedule::with('studio')->find($scheduleId);
        
        if (!$schedule) {
            return $this->errorResponse('Schedule not found', 404);
        }

        $seats = Seat::where('studio_id', $schedule->studio_id)->get();
        
        // Get booked seats for this schedule (including recent pending orders)
        $bookedSeats = OrderItem::whereHas('order', function($query) use ($scheduleId) {
            $query->where('schedule_id', $scheduleId)
                  ->where(function($q) {
                      $q->where('payment_status', 'paid')
                        ->orWhere(function($q2) {
                            $q2->where('payment_status', 'pending')
                               ->where('created_at', '>', now()->subMinutes(10));
                        });
                  });
        })->pluck('seat_id')->toArray();

        $seats = $seats->map(function($seat) use ($bookedSeats) {
            $seat->is_booked = in_array($seat->id, $bookedSeats);
            return $seat;
        });

        return $this->successResponse($seats, 'Seats layout retrieved successfully');
    }
}