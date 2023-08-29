<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $posts = Post::pluck('id')->toArray();
        foreach ($posts as $post) {
            \App\Models\Comment::factory(3)->create([
                'post_id' => $post,
            ]);
        }
    }
}
