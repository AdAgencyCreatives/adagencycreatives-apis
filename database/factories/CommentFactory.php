<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'user_id' => fake()->numberBetween(2, 30),
            'content' => fake()->text(),
            'created_at' => fake()->dateTimeBetween('-8 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
