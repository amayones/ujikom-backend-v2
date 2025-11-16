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
                'poster' => 'https://m.media-amazon.com/images/M/MV5BZTk5ODY0MmQtMzA3Ni00NGY1LThiYzItZThiNjFiNDM4MTM3XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk',
                'base_price' => 55000
            ],
            [
                'title' => 'Inside Out 2',
                'genre' => 'Animation, Family',
                'duration' => 96,
                'status' => 'play_now',
                'description' => 'Riley enters puberty and experiences a whole new set of emotions.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYTc1MDQ3NjAtOWEzMi00YzE1LWI2OWUtNjQ0OWJkMzI3MDhmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0',
                'base_price' => 45000
            ],
            [
                'title' => 'Dune: Part Two',
                'genre' => 'Sci-Fi, Adventure',
                'duration' => 166,
                'status' => 'play_now',
                'description' => 'Paul Atreides unites with Chani and the Fremen to seek revenge.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BN2QyZGU4ZDctOWMzMy00NTc5LThlOGQtODhmNDI1NmY5YzAwXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w',
                'base_price' => 52000
            ],
            [
                'title' => 'Wicked',
                'genre' => 'Fantasy, Musical',
                'duration' => 160,
                'status' => 'coming_soon',
                'description' => 'The untold story of the Witches of Oz before Dorothy arrived.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5YjY0MjktYjY5Yi00ZDNhLWI5M2YtZjQ5YjY0NWQ2NmQ4XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c',
                'base_price' => 50000
            ],
            [
                'title' => 'Moana 2',
                'genre' => 'Animation, Adventure',
                'duration' => 100,
                'status' => 'coming_soon',
                'description' => 'Moana embarks on a new oceanic adventure with her friends.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMTUxNDcxMzY5Ml5BMl5BanBnXkFtZTgwMzQyNjY3NjM@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE',
                'base_price' => 48000
            ],
            [
                'title' => 'Gladiator II',
                'genre' => 'Action, Drama',
                'duration' => 148,
                'status' => 'coming_soon',
                'description' => 'The epic saga continues with a new gladiator rising to power.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BYzljMGFhYzAtMWQzYi00MmMwLWI4NWEtYTgwNjQ5MzQ1OTg5XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0',
                'base_price' => 55000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}