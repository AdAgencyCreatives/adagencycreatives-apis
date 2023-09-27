<?php

namespace Database\Seeders;

use App\Models\Strength;
use Illuminate\Database\Seeder;

class StrengthSeeder extends Seeder
{
    public function run()
    {
        $strengths = [
            'Accountable',
            'Adventurous',
            'Ambitious',
            'Approachable',
            'Articulate',
            'Charismatic',
            'Clever',
            'Collaborative',
            'Conceptual',
            'Confident',
            'Compassion',
            'Competitive',
            'Cooperative',
            'Curious',
            'Dependable',
            'Design',
            'Devotion',
            'Diligent',
            'Easygoing',
            'Eloquent',
            'Enthusiastic',
            'Flexible',
            'Honesty',
            'Leadership',
            'Inquisitive',
            'Insightful',
            'Integrity',
            'Intuitive',
            'Meticulous',
            'Organized',
            'Patient',
            'Perceptive',
            'Persuasive',
            'Punctual',
            'Quiet',
            'Relaxed',
            'Resourceful',
            'Strategic',
            'Story-telling',
            'Humor',
            'Talkative',
            'Technological',
            'Pop-Culture',
            'Witty',
            'BIPOC',
        ];

        foreach ($strengths as $strength) {
            Strength::factory()->create(['name' => $strength]);
        }
    }
}