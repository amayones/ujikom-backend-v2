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
                'description' => 'Deadpool teams up with Wolverine in an epic adventure across the multiverse.',
                'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk',
                'base_price' => 55000
            ],
            [
                'title' => 'Inside Out 2',
                'genre' => 'Animation, Family',
                'duration' => 96,
                'status' => 'play_now',
                'description' => 'Riley enters puberty and experiences a whole new set of emotions.',
                'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0',
                'base_price' => 45000
            ],
            [
                'title' => 'Dune: Part Two',
                'genre' => 'Sci-Fi, Adventure',
                'duration' => 166,
                'status' => 'play_now',
                'description' => 'Paul Atreides unites with Chani and the Fremen to seek revenge.',
                'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w',
                'base_price' => 52000
            ],
            [
                'title' => 'Wicked',
                'genre' => 'Fantasy, Musical',
                'duration' => 160,
                'status' => 'coming_soon',
                'description' => 'The untold story of the Witches of Oz before Dorothy arrived.',
                'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c',
                'base_price' => 50000
            ],
            [
                'title' => 'Moana 2',
                'genre' => 'Animation, Adventure',
                'duration' => 100,
                'status' => 'coming_soon',
                'description' => 'Moana embarks on a new oceanic adventure with her friends.',
                'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE',
                'base_price' => 48000
            ],
            [
                'title' => 'Gladiator II',
                'genre' => 'Action, Drama',
                'duration' => 148,
                'status' => 'coming_soon',
                'description' => 'The epic saga continues with a new gladiator rising to power.',
                'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0',
                'base_price' => 55000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}