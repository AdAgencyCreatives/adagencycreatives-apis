<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CreativeFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'user_id' => fake()->randomElement([6, 7, 8, 9, 10]),
            'title' => fake()->jobTitle(),
            'years_of_experience' => fake()->randomElement(['Junior 0-2 years', 'Mid-level 2-5 years', 'Senior 5-10 years']),
            'type_of_work' => fake()->randomElement(['Freelance', 'Full-time', 'Part-time', 'Internship']),
            'about' => fake()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
