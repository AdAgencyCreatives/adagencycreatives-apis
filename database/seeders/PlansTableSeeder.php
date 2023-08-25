<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    public function run()
    {
        $plans = [

            [
                'name' => 'Post a Creative Job',
                'slug' => 'post-a-creative-job',
                'stripe_plan' => 'price_1NihRXB5Ooqa5ycAtfZ6GbKc',
                'price' => '149.0',
                'quota' => 1,
                'description' => 'One (1) Targeted Job Post Duration 30 Days Job Management Dashboard',
            ],
            [
                'name' => 'Multiple Creative Jobs',
                'slug' => 'multiple-creative-jobs',
                'stripe_plan' => 'price_1NihQwB5Ooqa5ycAgQhsJdPd',
                'price' => '349.0',
                'quota' => 3,
                'description' => '• Up to Three (3) Targeted Job Post• Duration 45 Days • Job Management Dashboard• Urgent Opportunities Option',
            ],
            [
                'name' => 'Premium Creative Jobs',
                'slug' => 'premium-creative-jobs',
                'stripe_plan' => 'price_1NihQQB5Ooqa5ycAlIRtHaJy',
                'price' => '699.0',
                'quota' => 5,
                'description' => 'Up to Five (5) Targeted Job Post• Duration 45 Days• Job Management Dashboard• Urgent Opportunities Option• Priority Posting',
            ],

        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
