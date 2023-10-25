<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
        return [
            // 'uuid' => fake()->uuid(),

            'receiver_id' => rand(2, 15),
            'message' => fake()->sentence(),
            'created_at' => fake()->dateTimeBetween('-5 days', 'now'),
            'updated_at' => now(),
        ];
    }
}
