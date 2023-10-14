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
            'Transportation',
            'Beverage | Spirits',
            'Finance',
            'Food',
            'Media',
            'Multicultural',
            'POP | Point of Purchase',
            'Public Relations',
        ];

        $industries2 = [
            '360 Activations',
            'Automotive and Transportation',
            'Beauty',
            'Beverage | Spirits',
            'Bilingual',
            'Branding',
            'CPG | Consumer Package Goods',
            'Digital | Social',
            'Entertainment',
            'Experiential',
            'Education',
            'Fashion',
            'Finance',
            'Food',
            'Government',
            'Healthcare',
            'Media',
            'Music and Arts',
            'Multicultural',
            'Influencer',
            'Insurance',
            'Non-Profit',
            'Packaging',
            'POP | Point of Purchase',
            'Public Relations',
            'QSR | Quick Serve Restaurant',
            'Retail',
            'Sports',
            'Technology',
            'Travel',
            'Traditional Agency',
            'Other',
        ];

        $finalArray = array_diff($industries2, $industries);

        foreach ($industries as $industry) {
            Industry::factory()->create([
                'name' => $industry,
                'slug' => Str::slug($industry),
            ]);
        }

        foreach ($finalArray as $industry) {
            Industry::factory()->create([
                'name' => $industry,
                'slug' => Str::slug($industry),
            ]);
        }
    }
}
