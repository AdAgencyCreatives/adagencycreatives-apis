<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => fake()->numberBetween(1, 20),
            'plan_id' => fake()->numberBetween(1, 3),
            'amount' => [149, 399, 699][rand(0, 2)],
            'created_at' => fake()->dateTimeBetween('-8 days', 'now'),
            'updated_at' => now(),
        ];
    }

    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
