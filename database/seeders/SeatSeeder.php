<?php

namespace Database\Seeders;

use App\Models\Seat;
use App\Models\Studio;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $studios = Studio::all();

        foreach ($studios as $studio) {
            $rows = ['A', 'B', 'C', 'D', 'E'];
            $columns = 10;

            foreach ($rows as $rowIndex => $row) {
                for ($col = 1; $col <= $columns; $col++) {
                    Seat::create([
                        'studio_id' => $studio->id,
                        'row' => $row,
                        'column' => $col
                    ]);
                }
            }
        }
    }
}