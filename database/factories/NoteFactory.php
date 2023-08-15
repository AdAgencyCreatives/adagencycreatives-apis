<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{

    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'body' => fake()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

    }
}
