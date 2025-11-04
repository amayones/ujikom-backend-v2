<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        $prices = [
            [
                'day_type' => 'weekday',
                'seat_category' => 'regular',
                'price' => 35000
            ],
            [
                'day_type' => 'weekday',
                'seat_category' => 'vip',
                'price' => 50000
            ],
            [
                'day_type' => 'weekend',
                'seat_category' => 'regular',
                'price' => 45000
            ],
            [
                'day_type' => 'weekend',
                'seat_category' => 'vip',
                'price' => 65000
            ]
        ];

        foreach ($prices as $price) {
            Price::create($price);
        }
    }
}