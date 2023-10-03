<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    public function run()
    {
        $industries = [
            'Alcohol',
            'Apparel',
            'Appliances',
            'Automotive',
            'B2B',
            'Beauty Care Products',
            'Beverage',
            'Baby Care',
            'Branding',
            'Broadcast Media',
            'Cannabis',
            'Convenient Store',
            'CPG | Consumer Package Goods',
            'Digital | Social',
            'Eco-Friendly',
            'Entertainment',
            'Experiential',
            'Educational',
            'Fashion',
            'Financial Services',
            'Food Quick Serve',
            'Food Package',
            'Food Health',
            'Footwear',
            'Gambling',
            'Games Product',
            'Government',
            'Grocery',
            'Health Organic Foods',
            'Healthcare Facilities',
            'Healthcare Products',
            'Humor Comedy',
            'Home Improvement',
            'Music and Arts',
            'Industrial Manufacturing',
            'Influencer Marketing',
            'Insurance',
            'Investment',
            'Jewelry',
            'Luxury',
            'Non-Profit',
            'Music',
            'Packaging',
            'Pet Food & Supplies',
            'Pharmaceutical',
            'Public Relations Media',
            'QSR | Quick Serve Restaurant',
            'Retail In0store',
            'Retail Online',
            'Real Estate',
            'Reproductive',
            'Retirement',
            'Sciences',
            'Sports',
            'Tobacco',
            'Technology Equipment',
            'Technology Online',
            'Telecommunications',
            'Target Marketing Infant',
            'Target Marketing Teen',
            'Target Marketing Adults',
            'Target Marketing Senior Adults',
            'Travel Business',
            'Travel Leisure',
            'UI / UX',
            'Video Gaming',
        ];

        foreach ($industries as $industry) {
            Industry::factory()->create([
                'name' => $industry,
                'slug' => Str::slug($industry),
            ]);
        }
    }
}
