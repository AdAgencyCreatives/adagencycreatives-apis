<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JobAlert;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobAlertSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 4)->pluck('id')->toArray();
        $category = Category::where('name', '3D Designer')->first();

        foreach ($users as $user) {
            JobAlert::factory()->create([
                'user_id' => $user,
                'category_id' => $category->id,
            ]);
        }

    }
}
