<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run()
    {
        $industries = [
            '360 Activation',
            'Automotive and Transportation',
            'Beauty',
            'Beverage | Spirits',
            'Bilingual',
            'Branding',
            'Broadcast Television',
            'Concepts',
            'CPG | Consumer Package Goods',
            'Digital | Social',
            'Education',
            'Entertainment',
            'Experiential',
            'Fashion',
            'Finance',
            'Food',
            'Government',
            'Healthcare',
            'In-store or Point-of-Sale',
            'Influencer',
            'Insurance',
            'Media',
            'Motion',
            'Multicultural',
            'Music and Arts',
            'Non-Profit',
            'Other',
            'Packaging',
            'POP | Point of Purchase',
            'Print and Out of Home',
            'Public Relations',
            'QSR | Quick Serve Restaurant',
            'Radio',
            'Retail',
            'Shopper Marketing',
            'Social',
            'Sports',
            'Strategy',
            'Technology',
            'Traditional Agency',
            'Travel',
            'UI User Interface Design',
            'UX User Experience Design',
        ];

        foreach ($industries as $industry) {
            Industry::factory()->create(['name' => $industry]);
        }
    }
}
