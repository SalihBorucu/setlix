<?php

namespace Database\Factories;

use App\Models\Band;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'band_id' => Band::factory(),
            'name' => fake()->words(rand(2, 5), true),
            'duration_seconds' => fake()->numberBetween(120, 600), // 2-10 minutes
            'notes' => fake()->optional(0.7)->paragraph(),
            'url' => fake()->optional(0.5)->url(),
        ];
    }
} 