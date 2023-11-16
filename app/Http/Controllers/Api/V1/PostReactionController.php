<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reaction\StorePostReactionRequest;
use App\Http\Resources\Reaction\PostReactionCollection;
use App\Http\Resources\Reaction\PostReactionResource;
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

        $existingReaction = PostReaction::withTrashed()
            ->where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingReaction) {
            // User has already reacted
            if ($existingReaction->trashed()) {
                // If the reaction is soft-deleted, restore it
                $existingReaction->restore();
            } elseif ($existingReaction->type !== $type) {
                // If the user is changing the reaction type, update it
                $existingReaction->update(['type' => $type]);
            } else {
                // If the user is clicking the same reaction type again, soft delete it
                $existingReaction->delete();
            }

            return response()->json(['message' => 'Reaction updated successfully']);
        }

        // User has not reacted yet, create a new reaction
        $data = [
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $post->id,
            'type' => $type,
        ];

        PostReaction::create($data);

        return response()->json(['message' => 'Reaction added successfully']);
    }
}
