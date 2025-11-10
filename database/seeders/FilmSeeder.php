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
                'poster' => 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg',
                'base_price' => 50000
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'genre' => 'Action, Adventure',
                'duration' => 148,
                'status' => 'play_now',
                'description' => 'Peter Parker seeks help from Doctor Strange.',
                'poster' => 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg',
                'base_price' => 45000
            ],
            [
                'title' => 'The Batman',
                'genre' => 'Action, Crime',
                'duration' => 176,
                'status' => 'coming_soon',
                'description' => 'A new take on the Dark Knight.',
                'poster' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'base_price' => 55000
            ],
            [
                'title' => 'Top Gun: Maverick',
                'genre' => 'Action, Drama',
                'duration' => 130,
                'status' => 'play_now',
                'description' => 'Maverick returns to train a new generation.',
                'poster' => 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg',
                'base_price' => 48000
            ],
            [
                'title' => 'Dune: Part Two',
                'genre' => 'Sci-Fi, Adventure',
                'duration' => 166,
                'status' => 'play_now',
                'description' => 'Paul Atreides unites with Chani and the Fremen.',
                'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'base_price' => 52000
            ],
            [
                'title' => 'Oppenheimer',
                'genre' => 'Biography, Drama',
                'duration' => 180,
                'status' => 'coming_soon',
                'description' => 'The story of J. Robert Oppenheimer.',
                'poster' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'base_price' => 50000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}