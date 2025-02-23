<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Setlist;
use App\Models\SetlistItem;
use App\Models\Song;
use Illuminate\Database\Seeder;

class SetlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bands = Band::all();

        foreach ($bands as $band) {
            // Create 2-3 setlists for each band
            for ($i = 0; $i < rand(2, 3); $i++) {
                $setlist = Setlist::create([
                    'band_id' => $band->id,
                    'name' => "Setlist " . ($i + 1) . " - " . $band->name,
                    'description' => "Sample setlist description " . ($i + 1),
                ]);

                // Get band's songs
                $bandSongs = $band->songs;
                
                // Create setlist items (mix of songs and breaks)
                $order = 1;
                
                // Add 8-12 items to each setlist
                for ($j = 0; $j < rand(8, 12); $j++) {
                    if (rand(0, 10) < 8) { // 80% chance of being a song
                        $song = $bandSongs->random();
                        SetlistItem::create([
                            'setlist_id' => $setlist->id,
                            'type' => 'song',
                            'song_id' => $song->id,
                            'title' => $song->name,
                            'duration_seconds' => $song->duration_seconds,
                            'notes' => "Performance notes for " . $song->name,
                            'order' => $order,
                        ]);
                    } else { // 20% chance of being a break
                        SetlistItem::create([
                            'setlist_id' => $setlist->id,
                            'type' => 'break',
                            'title' => 'Short Break',
                            'duration_seconds' => rand(300, 600), // 5-10 minutes
                            'notes' => 'Time for water and tuning',
                            'order' => $order,
                        ]);
                    }
                    $order++;
                }

                // Update total duration
                $setlist->total_duration = $setlist->calculateTotalDuration();
                $setlist->save();
            }
        }
    }
} 