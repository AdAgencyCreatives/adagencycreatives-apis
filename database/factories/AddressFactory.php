<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'label' => ['primary', 'business', 'personal'][rand(0, 2)],
            'street_1' => fake()->streetAddress,
            'street_2' => fake()->secondaryAddress,
            'city' => fake()->city,
            'state' => fake()->state,
            'country' => fake()->country,
            'postal_code' => fake()->postcode,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
