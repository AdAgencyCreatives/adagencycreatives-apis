<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Like\StoreLikeRequest;
use App\Http\Resources\Like\LikeCollection;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
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

        $likes = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new LikeCollection($likes);
    }

    public function store(StoreLikeRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $post = Post::where('uuid', $request->post_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        try {
            $like = Like::create($request->all());

            return ApiResponse::success($like, 200);
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Like $like)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Like $like)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Like $like)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Like $like)
    {
        //
    }
}
