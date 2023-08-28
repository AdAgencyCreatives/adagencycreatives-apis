<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Post::class)
                ->allowedFilters([
                    AllowedFilter::scope('user_id'),
                    AllowedFilter::scope('group_id'),
                    'status',
                ])
                ->allowedSorts('created_at');

        $posts = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new PostCollection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $group = Group::where('uuid', $request->group_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'group_id' => $group->id,
            'content' => $request->content,
            'status' => 'published',
        ]);

        try {
            $post = Post::create($request->all());

            if ($request->has('attachment_ids')) {
                $attachments = Attachment::whereIn('uuid', $request->attachment_ids)->get();
                $post->attachments()->saveMany($attachments);
            }

            return ApiResponse::success(new PostResource($post), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $post = Post::where('uuid', $uuid)->firstOrFail();

            return new PostResource($post);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function update(UpdatePostRequest $request, $uuid)
    {
        try {
            $post = Post::where('uuid', $uuid)->firstOrFail();
            $post->update($request->all());

            if ($request->has('attachment_ids')) {
                foreach ($post->attachments as $attachment) {
                    $attachment->delete();
                }

                $attachments = Attachment::whereIn('uuid', $request->attachment_ids)->get();
                $post->attachments()->saveMany($attachments);
            }

            return new PostResource($post);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $post = Post::where('uuid', $uuid)->firstOrFail();
            foreach ($post->attachments as $attachment) {
                $attachment->delete();
            }
            $post->delete();

            return ApiResponse::success(new PostResource($post), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
