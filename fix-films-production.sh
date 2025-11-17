#!/bin/bash

echo "🎬 Fixing Films in Production..."

# Update films with real posters and trailers
php artisan tinker --execute="
\$films = [
    [
        'title' => 'Avengers: Endgame',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'
    ],
    [
        'title' => 'Spider-Man: No Way Home',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'
    ],
    [
        'title' => 'The Batman',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMDIzNi00NGVlLWIzMDYtZTk3MTQ3NGQxZGEwXkEyXkFqcGdeQXVyMzMwOTU5MDk@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=mqqft2x_Aa4'
    ],
    [
        'title' => 'Top Gun: Maverick',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZWYzOGEwNTgtNWU3NS00ZTQ0LWJkODUtMmVhMjIwMjA1ZmQwXkEyXkFqcGdeQXVyMjkwOTAyMDU@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=giXco2jaZ_4'
    ],
    [
        'title' => 'Oppenheimer',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=uYPbbksJxIg'
    ],
    [
        'title' => 'Barbie',
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNjU3N2QxNzYtMjk1NC00MTc4LTk1NTQtMmUxNTljM2I0NDA5XkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=pBk4NYhWNMM'
    ]
];

foreach (\$films as \$filmData) {
    \$film = \App\Models\Film::where('title', \$filmData['title'])->first();
    if (\$film) {
        \$film->update([
            'poster' => \$filmData['poster'],
            'trailer' => \$filmData['trailer']
        ]);
        echo \"✓ Updated: {\$filmData['title']}\" . PHP_EOL;
    }
}

echo PHP_EOL . '✅ All films updated with real posters and trailers!' . PHP_EOL;
"

echo "✅ Films fixed successfully!"
