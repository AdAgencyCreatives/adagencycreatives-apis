<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'label' => ['linkedin', 'website', 'portfolio'][rand(0, 2)],
            'url' => fake()->url(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
