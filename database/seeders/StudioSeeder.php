<?php

namespace Database\Seeders;

use App\Models\Studio;
use Illuminate\Database\Seeder;

class StudioSeeder extends Seeder
{
    public function run(): void
    {
        $studios = [
            [
                'name' => 'Studio 1',
                'capacity' => 50
            ],
            [
                'name' => 'Studio 2',
                'capacity' => 50
            ],
            [
                'name' => 'Studio 3',
                'capacity' => 50
            ]
        ];

        foreach ($studios as $studio) {
            Studio::updateOrCreate(
                ['name' => $studio['name']],
                $studio
            );
        }
    }
}