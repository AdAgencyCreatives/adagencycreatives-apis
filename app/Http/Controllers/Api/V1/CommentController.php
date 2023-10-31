<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = QueryBuilder::for(Comment::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('post_id'),
            ])
            ->allowedSorts('created_at')->get();

        // Organize comments into a parent-child relationship array
        $commentsArray = [];
        foreach ($comments as $comment) {
            if ($comment->parent_id === null) {
                $commentsArray[$comment->id] = [
                    'comment' => $comment,
                    'replies' => [],
                ];
            } else {
                $commentsArray[$comment->parent_id]['replies'][] = $comment;
            }
        }

        return $commentsArray;

        // $comments = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new CommentCollection($comments);
    }

    public function store(StoreCommentRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $post = Post::where('uuid', $request->post_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        if ($request->has('parent_id')) {
            $parent_comment = Comment::where('uuid', $request->parent_id)->first();
            $request->merge([
                'parent_id' => $parent_comment->id,
            ]);
        }

        try {
            $comment = Comment::create($request->all());

            return ApiResponse::success(new CommentResource($comment), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    public function update(UpdateCommentRequest $request, $uuid)
    {
        try {
            $comment = Comment::where('uuid', $uuid)->firstOrFail();
            $comment->update($request->only('content'));

            return new CommentResource($comment);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $comment = Comment::where('uuid', $uuid)->firstOrFail();
            $comment->delete();

            return ApiResponse::success(new CommentResource($comment), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
