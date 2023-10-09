<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'home' => [
                'title' => 'Welcome to Ad Agency Creatives',
                'sub_title' => 'An all inclusive community for Advertising Agency Creatives',
                'searchbar_heading' => 'Search Creative Jobs',
                'searchbar_placeholder' => 'Search by name, title, location, company etc.',

                'motive_title_gather' => 'Gather',
                'motive_description_gather' => 'Social creative community',

                'motive_title_inspire' => 'Inspire',
                'motive_description_inspire' => 'Mentors and resources',

                'motive_title_do_cool_shit' => 'Do Cool $#*t!',
                'motive_description_do_cool_shit' => 'Creative jobs board',
            ],
            'footer' => [
                'title' => 'Gather. Inspire. Do Cool S#*t!',
            ],
            'community' => [
                'title' => 'The Lounge',
                'sub_title' => 'Creatives only. Ask for help. Offer or solicit advice. Share. Chat. Inspire. Tell jokes.',
            ],
            'about' => [
                'description' => 'Ad Agency Creatives come together

talk about the industry

talk about the work

meet other creatives

share ideas and resources

identify and manage job opportunities

do really do cool $#*t!',
            ],
        ];

        // Insert this data into pages table

        foreach ($data as $page_name => $page_data) {
            foreach ($page_data as $key => $value) {
                \App\Models\Page::create([
                    'page' => $page_name,
                    'key' => $key,
                    'value' => $value,
                ]);
            }

        }

    }
}
