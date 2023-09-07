<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\YearsOfExperience>
 */
class YearsOfExperienceFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
        ];
    }
}
