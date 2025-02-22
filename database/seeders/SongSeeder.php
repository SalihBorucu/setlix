<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Song;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    public function run(): void
    {
        $bands = Band::all();

        foreach ($bands as $band) {
            // Create 5-10 songs for each band
            $songCount = rand(5, 10);
            for ($i = 0; $i < $songCount; $i++) {
                Song::create([
                    'band_id' => $band->id,
                    'name' => "Song " . ($i + 1) . " - " . $band->name,
                    'duration_seconds' => rand(120, 300), // 2-5 minutes
                    'notes' => "Sample notes for song " . ($i + 1),
                    'artist' => "Artist " . ($i + 1),
                    'bpm' => rand(60, 180),
                ]);
            }
        }
    }
} 