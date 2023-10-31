<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'user_id' => fake()->randomElement([2, 3, 4, 5]),
            'name' => fake()->name(),
            'description' => fake()->text(),
            'status' => fake()->randomElement(['public', 'private', 'hidden']),
            'created_at' => fake()->dateTimeBetween('-8 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
