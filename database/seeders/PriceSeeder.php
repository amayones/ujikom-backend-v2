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
                'price' => 35000
            ],
            [
                'day_type' => 'weekend',
                'price' => 45000
            ]
        ];

        foreach ($prices as $price) {
            Price::create($price);
        }
    }
}