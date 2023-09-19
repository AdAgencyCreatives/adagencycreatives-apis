<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\YearsOfExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition()
    {
        $employementTypes = Job::EMPLOYMENT_TYPE;
        $salaryRanges = ['$30k - $50k', '$50k - $70k', '$70k - $100k'];
        $experiences = YearsOfExperience::pluck('name')->toArray();
        $applyTypes = ['Internal', 'External'];

        return [
            'uuid' => fake()->uuid(),
            'category_id' => fake()->numberBetween(1, 40),
            'state_id' => 1, //Alabama
            'city_id' => 2, //Birmingham
            'industry_experience' => fake()->numberBetween(1, 40),
            'media_experience' => fake()->numberBetween(10, 20),
            'title' => fake()->jobTitle,
            'description' => fake()->paragraph,
            'employment_type' => fake()->randomElement($employementTypes),
            'salary_range' => fake()->randomElement($salaryRanges),
            'years_of_experience' => fake()->randomElement($experiences),
            'apply_type' => fake()->randomElement($applyTypes),
            'external_link' => fake()->url,
            'status' => fake()->numberBetween(0, 1),
            'is_remote' => fake()->boolean,
            'is_hybrid' => fake()->boolean,
            'is_onsite' => fake()->boolean,
            'is_featured' => fake()->boolean,
            'is_urgent' => fake()->boolean,

            'expired_at' => fake()->dateTimeBetween('now', '+3 months'),
            'created_at' => fake()->dateTimeBetween('-8 days', 'now'),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
