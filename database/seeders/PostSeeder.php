<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        $groups = Group::pluck('id')->toArray();
        foreach ($groups as $group) {
            \App\Models\Post::factory(3)->create([
                'group_id' => $group,
            ]);
        }
    }
}
