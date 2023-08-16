<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    public function definition()
    {
        return [
            'uuid' => fake()->uuid(),
            'resource_type' => ['agencies', 'creatives', 'jobs', 'applications', 'posts'][rand(0, 4)],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
