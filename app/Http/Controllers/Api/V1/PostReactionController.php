<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reaction\StorePostReactionRequest;
use App\Http\Resources\Reaction\PostReactionCollection;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostReactionController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(PostReaction::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('post_id'),
                'type',
            ])
            ->allowedSorts('created_at');

        if ($request->per_page == -1) {
            // Fetch all records
            $reactions = $query->get();
        } else {
            // Paginate the results
            $reactions = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));
        }

        return new PostReactionCollection($reactions);
    }

    public function store(StorePostReactionRequest $request)
    {
        $user = $request->user();
        $post = Post::where('uuid', $request->post_id)->first();
        $type = $request->type;

        // Find all existing reactions of the user on the same post
        $existingReaction = PostReaction::withTrashed()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->where('type', $type)
            ->first();

        if ($existingReaction) {
            // User has already reacted

            if ($existingReaction->trashed()) {
                // If the reaction is soft-deleted, restore it
                $existingReaction->restore();
            } else {
                // If the user is clicking the same reaction type again, soft delete it
                $existingReaction->delete();
            }

            return response()->json(['message' => 'Reaction updated successfully']);
        }

        // User has not reacted yet, create a new reaction
        PostReaction::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type' => $type,
        ]);

        return response()->json(['message' => 'Reaction added successfully']);
    }
}
