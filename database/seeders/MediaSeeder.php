<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run()
    {

        $medias = [
            '360 Activation',
            'Advertising Traditional',
            'Animation',
            'Apps',
            'Bilingual (Spanish)',
            'Bilingual (Other)',
            'Brand Consulting',
            'Broadcast Television',
            'Business to Business',
            'Business to Consumer',
            'Corporate Communication',
            'Content Creation',
            'Digital Interactive',
            'Experiential Event Social',
            'Experiential Event Trade',
            'In-store Point-of-Sale',
            'Influencer Marketing',
            'Motion',
            'Packaging',
            'Political',
            'Print and Out of Home',
            'Product Development',
            'Radio',
            'Shopper Marketing',
            'Social',
            'Strategy',
            'Traditional Agency',
            'UI User Interface Design',
            'UX User Experience Design',
            'Website',
        ];

        foreach ($medias as $media) {
            Media::factory()->create(['name' => $media]);
        }
    }
}
