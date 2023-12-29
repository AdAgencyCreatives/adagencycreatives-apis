<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\Post\TrendingPostCollection;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Support\Facades\Cache;
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
            ->defaultSort('-created_at')
            ->allowedSorts('created_at');

        $posts = $query->with(['reactions' => function ($query) {
            // You can further customize the reactions query if needed
        }])
            ->whereHas('user') // If the user is deleted, don't show the attachment
            ->withCount('reactions')
            ->withCount('comments')
            ->withCount('likes')
            ->with('comments')
            // ->with('user.likes')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'))
            ->withQueryString();

        return new PostCollection($posts);
    }

    public function trending_posts(Request $request)
    {
        $cacheKey = 'trending_posts';
        // Attempt to retrieve the data from the cache
        $trendingPosts = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($request) {
            $query = QueryBuilder::for(Post::class)
                ->allowedFilters([
                    AllowedFilter::scope('user_id'),
                    AllowedFilter::scope('group_id'),
                ]);

            return $query
                ->whereHas('user')
                ->whereHas('group')
                ->withCount('reactions')
                ->withCount('comments')
                ->orderBy('reactions_count', 'desc')
                ->orderBy('comments_count', 'desc')
                ->orderBy('created_at', 'desc')
                ->with('comments')
                ->withCount('reactions')
                ->paginate($request->per_page ?? config('global.request.pagination_limit'))
                ->withQueryString();
        });

        $authenticatedUserId = auth()->id();

        $trendingPosts->getCollection()->transform(function ($post) use ($authenticatedUserId) {
            $post->user_has_liked = $post->likes->contains('user_id', $authenticatedUserId);

            return $post;
        });

        return new TrendingPostCollection($trendingPosts);
    }

    public function store(StorePostRequest $request)
    {
        $user = $request->user();
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
            return ApiResponse::error('PS-01' . $e->getMessage(), 400);
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
                $new_attachments = Attachment::whereIn('uuid', $request->attachment_ids)->get();
                $post_existing_attachments = Attachment::where('resource_id', $post->id)->get();

                // delete those attachments which are not in new_attachments
                foreach ($post_existing_attachments as $post_existing_attachment) {
                    if (!$new_attachments->contains('uuid', $post_existing_attachment->uuid)) {
                        $post_existing_attachment->delete();
                    }
                }
                $post->attachments()->saveMany($new_attachments);
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

    public function main_feed(Request $request)
    {
        try {
            $user = request()->user();
            $feed_group = Group::where('slug', 'feed')->first();
            $joined_groups = GroupMember::where('user_id', $user->id)->pluck('id')->toArray();

            $query = QueryBuilder::for(Post::class)
                ->allowedFilters([
                    AllowedFilter::scope('user_id'),
                    AllowedFilter::scope('group_id'),
                    'status',
                ])
                ->defaultSort('-created_at')
                ->allowedSorts('created_at')
                ->whereIn('group_id', array_merge([$feed_group->id], $joined_groups));

            return array_merge([$feed_group->id], $joined_groups);

            $posts = $query->with(['reactions' => function ($query) {
                // You can further customize the reactions query if needed
            }])
                ->whereHas('user') // If the user is deleted, don't show the attachment
                ->withCount('reactions')
                ->withCount('comments')
                ->withCount('likes')
                ->with('comments')
                // ->with('user.likes')
                ->paginate($request->per_page ?? config('global.request.pagination_limit'))
                ->withQueryString();

            return new PostCollection($posts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
