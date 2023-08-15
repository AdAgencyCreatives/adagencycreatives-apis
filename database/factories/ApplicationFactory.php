<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement([0, 1, 2]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
