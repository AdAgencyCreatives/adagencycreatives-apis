<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{

    public function run()
    {
        $agencies = User::where('role', 3)->pluck('id');

        $employementTypes = ['Full-time', 'Part-time', 'Contract', 'Freelance'];
        $industryExperiences = ['Entry Level', 'Mid Level', 'Senior Level'];
        $mediaExperiences = ['Digital', 'Print', 'Broadcast'];
        $salaryRanges = ['$30k - $50k', '$50k - $70k', '$70k - $100k'];
        $experiences = ['Junior 0-2 years', 'Mid-level 2-5 years', 'Senoir 5-10 years', 'Director 10+ years', 'Executive 15+ years'];

        return [
            'uuid' => fake()->uuid(),
            'user_id' => User::inRandomOrder()->first()->id,
            'address_id' => fake()->randomNumber(),
            'title' => fake()->jobTitle,
            'description' => fake()->paragraph,
            'category' => fake()->word,
            'employement_type' => fake()->randomElement($employementTypes),
            'industry_experience' => fake()->randomElement($industryExperiences),
            'media_experience' => fake()->randomElement($mediaExperiences),
            'salary_range' => fake()->randomElement($salaryRanges),
            'experience' => fake()->randomElement($experiences),
            'apply_type' => ['internal', 'external'][rand(0, 1)],
            'external_link' => fake()->url,
            'status' => fake()->numberBetween(0, 1),
            'is_remote' => fake()->boolean,
            'is_hybrid' => fake()->boolean,
            'is_onsite' => fake()->boolean,
            'is_featured' => fake()->boolean,
            'is_urgent' => fake()->boolean,
            'expired_at' => fake()->dateTimeBetween('now', '+3 months'),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];

    }
}
