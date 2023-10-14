<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'In-store or Point-of-Sale',
            'Bilingual',
            '360 Activation',
            'Concepts',
            'Digital',
        ];

        $secondMedias = [
            '360 Activation',
            'Bilingual',
            'Branding',
            'Broadcast Television',
            'Concepts',
            'Digital',
            'Experiential',
            'In-store or Point-of-Sale',
            'Motion',
            'Packaging',
            'Print and Out of Home',
            'Radio',
            'Shopper Marketing',
            'Social',
            'Strategy',
            'Traditional Agency',
            'UI User Interface Design',
            'UX User Experience Design',
            'Branding',
            'Experiential',
        ];

        $finalArray = array_diff($secondMedias, $medias);

        foreach ($medias as $media) {
            Media::factory()->create([
                'name' => $media,
                'slug' => Str::slug($media),
            ]);
        }
    }
}
