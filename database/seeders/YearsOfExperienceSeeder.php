<?php

namespace Database\Seeders;

use App\Models\YearsOfExperience;
use Illuminate\Database\Seeder;

class YearsOfExperienceSeeder extends Seeder
{
    public function run()
    {
        $experiences = [
            'Junior 0-2 years',
            'Mid-level 2-5 years',
            'Senior 5-10 years',
            'Director 10+ years',
        ];

        foreach ($experiences as $experience) {
            YearsOfExperience::factory()->create(['name' => $experience]);
        }
    }
}
