<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'degree' => fake()->word,
            'college' => fake()->sentence,
            'started_at' => fake()->dateTimeThisDecade,
            'completed_at' => fake()->optional()->dateTimeThisDecade,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
