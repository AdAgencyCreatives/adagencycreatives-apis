<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'resource_type' => ['profile_picture', 'agency_logo', 'resume', 'creative_spotlight'][rand(0, 3)],
            'path' => fake()->imageUrl,
            'name' => fake()->word,
            'extension' => 'jpg',
            'created_at' => fake()->dateTimeBetween('-8 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
