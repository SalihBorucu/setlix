<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Band;
use App\Models\Song;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BandSeeder::class,
            SongSeeder::class,
            SetlistSeeder::class,
            BlogSeeder::class, // Seed sample blog data
        ]);
    }
}
