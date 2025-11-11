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
                'title' => 'Deadpool & Wolverine',
                'genre' => 'Action, Comedy',
                'duration' => 128,
                'status' => 'play_now',
                'description' => 'Deadpool teams up with Wolverine in an epic adventure.',
                'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'base_price' => 55000
            ],
            [
                'title' => 'Inside Out 2',
                'genre' => 'Animation, Family',
                'duration' => 96,
                'status' => 'play_now',
                'description' => 'Riley enters puberty and new emotions appear.',
                'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'base_price' => 45000
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
                'title' => 'Wicked',
                'genre' => 'Fantasy, Musical',
                'duration' => 160,
                'status' => 'coming_soon',
                'description' => 'The untold story of the Witches of Oz.',
                'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg',
                'base_price' => 50000
            ],
            [
                'title' => 'Moana 2',
                'genre' => 'Animation, Adventure',
                'duration' => 100,
                'status' => 'coming_soon',
                'description' => 'Moana embarks on a new oceanic adventure.',
                'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg',
                'base_price' => 48000
            ],
            [
                'title' => 'Gladiator II',
                'genre' => 'Action, Drama',
                'duration' => 148,
                'status' => 'coming_soon',
                'description' => 'The epic saga continues with a new gladiator.',
                'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
                'base_price' => 55000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}