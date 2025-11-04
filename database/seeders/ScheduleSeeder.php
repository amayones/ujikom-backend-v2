<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Film;
use App\Models\Studio;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $films = Film::where('status', 'play_now')->get();
        $studios = Studio::all();
        
        $times = ['10:00', '13:00', '16:00', '19:00', '22:00'];
        
        // Track used slots to avoid conflicts
        $usedSlots = [];
        
        foreach ($films as $filmIndex => $film) {
            for ($day = 0; $day < 7; $day++) {
                $date = Carbon::now()->addDays($day);
                
                // Each film gets 2-3 showtimes per day
                $filmTimes = array_slice($times, $filmIndex % 3, 2);
                
                foreach ($filmTimes as $time) {
                    // Rotate studios to avoid conflicts
                    $studioIndex = ($filmIndex + $day) % $studios->count();
                    $studio = $studios[$studioIndex];
                    
                    $slotKey = $studio->id . '_' . $date->format('Y-m-d') . '_' . $time;
                    
                    // Skip if slot already used
                    if (isset($usedSlots[$slotKey])) {
                        continue;
                    }
                    
                    Schedule::create([
                        'film_id' => $film->id,
                        'studio_id' => $studio->id,
                        'show_time' => $date->format('Y-m-d') . ' ' . $time . ':00'
                    ]);
                    
                    $usedSlots[$slotKey] = true;
                }
            }
        }
    }
}