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
                'stripe_plan' => 'price_1Ny3PtB5Ooqa5ycAsjpIMQl0',
                'price' => 149,
                'quota' => 1,
                'days' => 30,
                'description' => 'One (1) Targeted Job Post Duration 30 Days Job Management Dashboard',
            ],
            [
                'name' => 'Multiple Creative Jobs',
                'slug' => 'multiple-creative-jobs',
                'stripe_plan' => 'price_1NihQwB5Ooqa5ycAgQhsJdPd',
                'price' => 349,
                'quota' => 3,
                'days' => 45,
                'description' => '• Up to Three (3) Targeted Job Post• Duration 45 Days • Job Management Dashboard• Urgent Opportunities Option',
            ],
            [
                'name' => 'Premium Creative Jobs',
                'slug' => 'premium-creative-jobs',
                'stripe_plan' => 'price_1NihQQB5Ooqa5ycAlIRtHaJy',
                'price' => 649,
                'quota' => 5,
                'days' => 45,
                'description' => 'Up to Five (5) Targeted Job Post• Duration 45 Days• Job Management Dashboard• Urgent Opportunities Option• Priority Posting',
            ],
            [
                'name' => 'Annual Plan with Monthly Quota',
                'slug' => 'annual-monthly-quota',
                'stripe_plan' => 'price_1NihQQB5Ooqa5ycAlIRtHaJy', // REPLACE with your new Stripe plan ID
                'price' => 649,
                'quota' => 60,
                'days' => 365,
                'description' => 'Five (5) job posts per month for one year. Quota resets monthly.',
            ],

        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate($plan);
        }
    }
}
