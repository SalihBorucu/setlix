<?php

namespace Database\Seeders;

use App\Models\Band;
use App\Models\User;
use Illuminate\Database\Seeder;

class BandSeeder extends Seeder
{
    public function run(): void
    {
        $mainUser = User::where('email', 'test@example.com')->first();
        $otherUsers = User::where('email', '!=', 'test@example.com')->get();

        // Create 3 bands where main user is admin
        for ($i = 0; $i < 3; $i++) {
            $band = Band::create([
                'name' => "Test Band " . ($i + 1),
                'description' => "This is a test band description " . ($i + 1),
            ]);

            // Add main user as admin
            $band->members()->attach($mainUser, ['role' => 'admin']);

            // Add 2-3 random users as members
            $randomUsers = $otherUsers->random(rand(2, 3));
            foreach ($randomUsers as $user) {
                $band->members()->attach($user, ['role' => 'member']);
            }
        }

        // Create 2 bands where main user is regular member
        for ($i = 0; $i < 2; $i++) {
            $band = Band::create([
                'name' => "Member Band " . ($i + 1),
                'description' => "Band where test user is regular member " . ($i + 1),
            ]);

            // Add main user as member
            $band->members()->attach($mainUser, ['role' => 'member']);

            // Add another random user as admin
            $band->members()->attach($otherUsers->random(1), ['role' => 'admin']);
        }
    }
} 