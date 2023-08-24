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
                'name' => 'Premium Creative Jobs',
                'slug' => 'premium-creative-jobs',
                'stripe_plan' => 'price_1NiKxbB5Ooqa5ycAykLDa3J8',
                'price' => '699.0',
                'description' => 'Up to Five (5) Targeted Job Post• Duration 45 Days• Job Management Dashboard• Urgent Opportunities Option• Priority Posting',
            ],
            [
                'name' => 'Multiple Creative Jobs',
                'slug' => 'multiple-creative-jobs',
                'stripe_plan' => 'price_1NiKxnB5Ooqa5ycAmlJ4k3rF',
                'price' => '349.0',
                'description' => '• Up to Three (3) Targeted Job Post• Duration 45 Days • Job Management Dashboard• Urgent Opportunities Option',
            ],
            [
                'name' => 'Post a Creative Job',
                'slug' => 'post-a-creative-job',
                'stripe_plan' => 'price_1NiKy2B5Ooqa5ycAJyp3iR5y',
                'price' => '149.0',
                'description' => 'One (1) Targeted Job Post Duration 30 Days Job Management Dashboard',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
