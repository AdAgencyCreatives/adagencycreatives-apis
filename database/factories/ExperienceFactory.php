<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'title' => fake()->jobTitle,
            'company' => fake()->company,
            'description' => fake()->paragraph,
            'started_at' => fake()->dateTimeThisDecade,
            'completed_at' => fake()->optional()->dateTimeThisDecade,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
