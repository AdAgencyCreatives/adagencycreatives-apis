<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return view('pages.posts.index');
    }

    public function details($uuid)
    {
        $post = Post::where('uuid', $uuid)->first();

        $post->load(['attachments', 'comments.user']);
        // dd($post);
        return view('pages.posts.detail', compact('post'));
    }
}
