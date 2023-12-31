<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'password' => '$P$BKyDIOxEwk1Jr0kwqdGG5TVQbOI4CS0', // password
            'role' => fake()->randomElement([3, 4]),
            'status' => fake()->randomElement(['pending', 'active', 'inactive']),
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
