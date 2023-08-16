<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            '3D Designer',
            'Art Director',
            'Associate Creative Director Art Director',
            'Associate Creative Director Copywriter',
            'Chief Creative Officer (CCO)',
            'Copywriter',
            'Creative Director (Art Director or Copywriter)',
            'Creative Director Art Director',
            'Creative Director Copywriter',
            'Creative Team Art Director',
            'Creative Team Copywriter',
            'Digital Designer',
            'Director Motion Graphic Design',
            'Director of Digital Design',
            'Director of Graphic Design',
            'Director of Production',
            'Executive Creative Director (Art Director or Copywriter)',
            'Executive Creative Director Art Director',
            'Executive Creative Director Copywriter',
            'Graphic Designer',
            'Group Creative Director (Art Director or Copywriter)',
            'Group Creative Director Art Director',
            'Group Creative Director Copywriter',
            'Illustrator | Illustration Designer',
            'Intern Art Director',
            'Intern Copywriter',
            'Intern Graphic Designer',
            'Internship',
            'Junior Art Director',
            'Junior Copywriter',
            'Junior Digital Designer',
            'Junior Graphic Designer',
            'Junior Motion Graphics Designer',
            'Motion Graphic Designer',
            'Production Artist',
            'Senior Art Director',
            'Senior Copywriter',
            'Senior Digital Designer',
            'Senior Graphic Designer',
            'Senior Motion Graphic Designer',
            'Senior Production Artist',
            'Senior UI User Interface Designer',
            'Senior UX User Experience Designer',
            'Senior Web Designer',
            'Senior Web Developer',
            'UI User Interface Designer',
            'UX User Experience Designer',
            'Web Designer',
            'Web Developer',
        ];

        foreach ($categories as $category) {
            Category::factory()->create(['name' => $category]);
        }
    }
}
