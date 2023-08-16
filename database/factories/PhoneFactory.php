<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'label' => ['primary', 'business', 'personal'][rand(0, 2)],
            'country_code' => '+'.$this->faker->randomElement(['1', '91', '44', '92']),
            'phone_number' => fake()->phoneNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
