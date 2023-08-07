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
            'user_id' => fake()->randomElement([1, 2, 3, 4, 5]),
            'name' => fake()->name(),
            'attachment_id' => null,
            'about' => fake()->paragraph(),
            'size' => fake()->randomElement([10, 50, 100, 500]),
            'type_of_work' => fake()->randomElement(['Freelance', 'Full-time', 'Part-time', 'Internship']),
            'industry_specialty' => '1,2,3,4',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
