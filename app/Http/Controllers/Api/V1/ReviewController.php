<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Resources\Review\ReviewCollection;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Review;
use App\Models\reviews;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Review::class)
            ->allowedFilters([
                AllowedFilter::scope('target_id'),
            ])
            ->allowedSorts('created_at');

        $reviews = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ReviewCollection($reviews);
    }

    public function store(StoreReviewRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $target = User::where('uuid', $request->target_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'target_id' => $target->id,
        ]);

        try {
            $review = Review::create($request->all());

            return ApiResponse::success(new ReviewResource($review), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    public function show(reviews $reviews)
    {
        //
    }

    public function edit(reviews $reviews)
    {
        //
    }

    public function update(Request $request, reviews $reviews)
    {
        //
    }

    public function destroy(reviews $reviews)
    {
        //
    }
}
