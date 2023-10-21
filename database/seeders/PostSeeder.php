<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Post;
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

        $posts = Post::select(['id', 'user_id'])->get();
        foreach ($posts as $post) {
            // \App\Models\Attachment::factory(1)->create([
            //     'user_id' => $post->user_id,
            //     'resource_id' => $post->id,
            // ]);
        }
    }
}