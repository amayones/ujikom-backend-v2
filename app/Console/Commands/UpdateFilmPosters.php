<?php

namespace App\Console\Commands;

use App\Models\Film;
use Illuminate\Console\Command;

class UpdateFilmPosters extends Command
{
    protected $signature = 'films:update-posters';
    protected $description = 'Update film posters to TMDB URLs';

    public function handle()
    {
        $updates = [
            'Deadpool & Wolverine' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk'
            ],
            'Inside Out 2' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0'
            ],
            'Dune: Part Two' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w'
            ],
            'Wicked' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c'
            ],
            'Moana 2' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE'
            ],
            'Gladiator II' => [
                'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
                'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0'
            ]
        ];

        foreach ($updates as $title => $data) {
            $film = Film::where('title', $title)->first();
            if ($film) {
                $film->update($data);
                $this->info("Updated: {$title}");
            } else {
                $this->warn("Not found: {$title}");
            }
        }

        $this->info('All films updated successfully!');
        return 0;
    }
}
