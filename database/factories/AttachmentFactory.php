<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'resource_type' => ['profile_picture', 'logo', 'resume', 'creative_spotlight'][rand(0, 3)],
            'path' => fake()->imageUrl,
            'extension' => 'jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
