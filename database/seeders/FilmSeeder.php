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
                'description' => 'After the devastating events of Infinity War, the Avengers assemble once more to reverse Thanos actions and restore balance to the universe.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c',
                'base_price' => 55000
            ],
            [
                'title' => 'Spider-Man: No Way Home',
                'genre' => 'Action, Adventure',
                'duration' => 148,
                'status' => 'play_now',
                'description' => 'Peter Parker seeks Doctor Stranges help to make the world forget he is Spider-Man, but the spell goes wrong.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA',
                'base_price' => 50000
            ],
            [
                'title' => 'The Batman',
                'genre' => 'Action, Crime',
                'duration' => 176,
                'status' => 'play_now',
                'description' => 'Batman ventures into Gothams underworld when a sadistic killer leaves behind a trail of cryptic clues.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMDIzNi00NGVlLWIzMDYtZTk3MTQ3NGQxZGEwXkEyXkFqcGdeQXVyMzMwOTU5MDk@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=mqqft2x_Aa4',
                'base_price' => 52000
            ],
            [
                'title' => 'Top Gun: Maverick',
                'genre' => 'Action, Drama',
                'duration' => 130,
                'status' => 'coming_soon',
                'description' => 'After thirty years, Maverick is still pushing the envelope as a top naval aviator.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BZWYzOGEwNTgtNWU3NS00ZTQ0LWJkODUtMmVhMjIwMjA1ZmQwXkEyXkFqcGdeQXVyMjkwOTAyMDU@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=giXco2jaZ_4',
                'base_price' => 48000
            ],
            [
                'title' => 'Oppenheimer',
                'genre' => 'Biography, Drama',
                'duration' => 180,
                'status' => 'coming_soon',
                'description' => 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=uYPbbksJxIg',
                'base_price' => 55000
            ],
            [
                'title' => 'Barbie',
                'genre' => 'Adventure, Comedy',
                'duration' => 114,
                'status' => 'coming_soon',
                'description' => 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.',
                'poster' => 'https://m.media-amazon.com/images/M/MV5BNjU3N2QxNzYtMjk1NC00MTc4LTk1NTQtMmUxNTljM2I0NDA5XkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_SX300.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=pBk4NYhWNMM',
                'base_price' => 45000
            ]
        ];

        foreach ($films as $film) {
            Film::create($film);
        }
    }
}