<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Like\StoreLikeRequest;
use App\Http\Resources\Like\LikeCollection;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class LikeController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Like::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('post_id'),
            ])
            ->allowedSorts('created_at');

        $likes = $query->get();
        // $likes = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new LikeCollection($likes);
    }

    public function store(StoreLikeRequest $request)
    {
        $user = $request->user();
        $post = Post::where('uuid', $request->post_id)->first();

        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        try {
            if ($existingLike) {
                $existingLike->delete();
                $response = 'Like removed';
                $statusCode = 200;
            } else {
                $like = Like::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
                $response = $like;
                $statusCode = 200;
            }

            return ApiResponse::success($response, $statusCode);
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    public function destroy(Like $like)
    {

    }
}
