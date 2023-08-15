<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'resource_type' => ['profile_image','logo', 'resume',][rand(0,2)],         
            'path' => fake()->imageUrl,
            'extension' => 'jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
