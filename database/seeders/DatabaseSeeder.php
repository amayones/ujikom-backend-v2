<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            StudioSeeder::class,
            FilmSeeder::class,
            SeatSeeder::class,
            PriceSeeder::class,
            ScheduleSeeder::class,
        ]);
        
        // OrderSeeder is optional (sample data)
        try {
            $this->call(OrderSeeder::class);
        } catch (\Exception $e) {
            $this->command->warn('OrderSeeder skipped: ' . $e->getMessage());
        }
    }
}
