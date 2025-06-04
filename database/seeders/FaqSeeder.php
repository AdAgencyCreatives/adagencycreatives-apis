<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $faqs = [
        ];

        foreach ($faqs as $faq) {
            Faq::factory()->create([
                'title' => $faq['title'],
                'description' => $faq['description'],
                'order' => $faq['order'] ?? 0,
            ]);
        }
    }
}
