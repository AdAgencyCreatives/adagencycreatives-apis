<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

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

    public function update(Request $request, $id)
    {
        Post::where('id', $id)->update([
            'created_at' => $request->created_at,
        ]);

        return redirect()->back()->with('success', 'Post updated successfully');
    }

}
