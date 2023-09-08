<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->name(),
            'about' => fake()->paragraph(),
            'size' => fake()->randomElement([10, 50, 100, 500]),
            'industry_experience' => '1,2,3,4',
            'media_experience' => '1,2,3,4',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
