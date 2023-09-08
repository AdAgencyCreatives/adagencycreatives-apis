<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition()
    {
        $employementTypes = ['Full-time', 'Part-time', 'Contract', 'Freelance'];
        $salaryRanges = ['$30k - $50k', '$50k - $70k', '$70k - $100k'];
        $experiences = ['Junior 0-2 years', 'Mid-level 2-5 years', 'Senior 5-10 years', 'Director 10+ years', 'Executive 15+ years', 'Chief Executive 20+ years'];
        $applyTypes = ['Internal', 'External'];

        return [
            'uuid' => fake()->uuid(),
            'category_id' => fake()->numberBetween(1, 40),
            'industry_experience' => fake()->numberBetween(1, 40),
            'media_experience' => fake()->numberBetween(10, 20),
            'title' => fake()->jobTitle,
            'description' => fake()->paragraph,
            'employement_type' => fake()->randomElement($employementTypes),
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
