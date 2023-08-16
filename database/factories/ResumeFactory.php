<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ResumeFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'years_of_experience' => ['Junior 0-2 years', 'Mid-level 2-5 years', 'Senior 5-10 years', 'Director 10+ years', 'Executive 15+ years', 'Chief Executive 20+ years'][rand(0, 5)],
            'about' => fake()->paragraph,
            'industry_specialty' => fake()->numberBetween(1, 40),
            'media_experience' => fake()->numberBetween(10, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
