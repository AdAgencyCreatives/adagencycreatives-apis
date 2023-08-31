<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
