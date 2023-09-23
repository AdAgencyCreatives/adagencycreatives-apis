<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'street_1' => fake()->streetAddress,
            'street_2' => fake()->secondaryAddress,
            'city_id' => 39,
            'state_id' => 30,
            'country_id' => 1,
            'postal_code' => fake()->postcode,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
