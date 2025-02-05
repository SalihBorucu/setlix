<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Setlist;
use Illuminate\Database\Seeder;

class SetlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create setlists for each band
        Band::all()->each(function ($band) {
            // Create 2-4 setlists for each band
            $setlistCount = rand(2, 4);
            
            for ($i = 0; $i < $setlistCount; $i++) {
                // Create setlist
                $setlist = Setlist::create([
                    'band_id' => $band->id,
                    'name' => $this->getSetlistName($i),
                    'description' => fake()->paragraph(),
                    'total_duration' => 0 // Will be calculated after adding songs
                ]);

                // Get random songs from the band (3-8 songs per setlist)
                $songs = $band->songs()->inRandomOrder()->take(rand(3, 8))->get();
                
                // Attach songs with order
                foreach ($songs as $index => $song) {
                    $setlist->songs()->attach($song->id, [
                        'order' => $index,
                        'notes' => rand(0, 1) ? fake()->sentence() : null // 50% chance of having notes
                    ]);
                }

                // Update total duration
                $setlist->updateTotalDuration();
            }
        });
    }

    /**
     * Get a creative setlist name based on index
     */
    private function getSetlistName(int $index): string
    {
        $names = [
            'Main Set',
            'Acoustic Night',
            'Greatest Hits',
            'New Material',
            'Fan Favorites',
            'Extended Set',
            'Festival Set',
            'Club Night Mix',
            'Unplugged Session',
            'Rock Night Special'
        ];

        return $names[$index] ?? fake()->words(2, true) . ' Set';
    }
} 