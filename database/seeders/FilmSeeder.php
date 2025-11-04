<?php

namespace Database\Seeders;

use App\Models\Film;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    public function run(): void
    {
        $films = [
            [
                'title' => 'Avengers: Endgame',
                'genre' => 'Action, Adventure',
                'duration' => 181,
                'status' => 'play_now',
                'description' => 'The epic conclusion to the Infinity Saga.',
                'poster' => 'https://via.placeholder.com/300x450/1a1a2e/eaeaea?text=Avengers+Endgame',
                'base_price' => 50000
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'genre' => 'Action, Adventure',
                'duration' => 148,
                'status' => 'play_now',
                'description' => 'Peter Parker seeks help from Doctor Strange.',
                'poster' => 'https://via.placeholder.com/300x450/c1121f/ffffff?text=Spider-Man+NWH',
                'base_price' => 45000
            ],
            [
                'title' => 'The Batman',
                'genre' => 'Action, Crime',
                'duration' => 176,
                'status' => 'coming_soon',
                'description' => 'A new take on the Dark Knight.',
                'poster' => 'https://via.placeholder.com/300x450/000000/dc143c?text=The+Batman',
                'base_price' => 55000
            ],
            [
                'title' => 'Top Gun: Maverick',
                'genre' => 'Action, Drama',
                'duration' => 130,
                'status' => 'play_now',
                'description' => 'Maverick returns to train a new generation.',
                'poster' => 'https://via.placeholder.com/300x450/003566/ffd60a?text=Top+Gun+Maverick',
                'base_price' => 48000
            ],
            [
                'title' => 'Dune: Part Two',
                'genre' => 'Sci-Fi, Adventure',
                'duration' => 166,
                'status' => 'play_now',
                'description' => 'Paul Atreides unites with Chani and the Fremen.',
                'poster' => 'https://via.placeholder.com/300x450/d4a373/000000?text=Dune+Part+Two',
                'base_price' => 52000
            ],
            [
                'title' => 'Oppenheimer',
                'genre' => 'Biography, Drama',
                'duration' => 180,
                'status' => 'coming_soon',
                'description' => 'The story of J. Robert Oppenheimer.',
                'poster' => 'https://via.placeholder.com/300x450/ff6700/ffffff?text=Oppenheimer',
                'base_price' => 50000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}