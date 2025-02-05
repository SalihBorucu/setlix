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
        // Create a test user with known credentials
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 4 more random users
        $users = User::factory(4)->create();
        $allUsers = $users->push($testUser);

        // Create some bands
        $bands = [
            [
                'name' => 'The Code Breakers',
                'description' => 'A progressive rock band that combines technical prowess with melodic sensibility.',
            ],
            [
                'name' => 'Syntax Error',
                'description' => 'High-energy punk rock with a touch of electronic elements.',
            ],
            [
                'name' => 'Runtime Exception',
                'description' => 'Heavy metal band known for their complex arrangements and technical solos.',
            ],
            [
                'name' => 'The Null Pointers',
                'description' => 'An indie rock band with influences from jazz and blues.',
            ],
        ];

        foreach ($bands as $bandData) {
            $band = Band::create($bandData);
            
            // Randomly assign 2-4 members to each band
            $members = $allUsers->random(rand(2, 4));
            
            foreach ($members as $index => $member) {
                // First member is always admin
                $role = $index === 0 ? 'admin' : 'member';
                $band->members()->attach($member, ['role' => $role]);
            }
        }

        // Make sure test user is admin of at least one band
        $testUserBand = Band::create([
            'name' => 'Test User\'s Band',
            'description' => 'A band created specifically for testing admin functionality.',
        ]);
        $testUserBand->members()->attach($testUser, ['role' => 'admin']);
        
        // Add some random members to test user's band
        $randomMembers = $users->random(2);
        foreach ($randomMembers as $member) {
            $testUserBand->members()->attach($member, ['role' => 'member']);
        }

        // Create songs for each band
        Band::all()->each(function ($band) {
            // Create 5-10 songs for each band
            Song::factory()
                ->count(rand(5, 10))
                ->create(['band_id' => $band->id]);
        });

        // Create setlists for each band
        $this->call([
            SetlistSeeder::class,
        ]);
    }
}
