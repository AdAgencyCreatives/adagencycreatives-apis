<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Resources\Review\ReviewCollection;
use App\Http\Resources\Review\ReviewResource;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = QueryBuilder::for(Review::class)
            ->allowedFilters([
                AllowedFilter::scope('target_id'),
            ])
            ->allowedSorts('created_at');

        if(!$request->is_own_profile){
            $query = $query->where('user_id', $user->id);
        }

        $reviews = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ReviewCollection($reviews);
    }

    public function store(StoreReviewRequest $request)
    {
        $user = $request->user();
        $target = User::where('uuid', $request->target_id)->first();

        if ($user->id === $target->id) {
            return response()->json(['error' => 'You cannot give a review to yourself.'], 422);
        }

        $existingReview = Review::where('user_id', $user->id)
            ->where('target_id', $target->id)
            ->first();

        $requestData = $request->all();
        $requestData['uuid'] = Str::uuid();
        $requestData['user_id'] = $user->id;
        $requestData['target_id'] = $target->id;

        try {
            if ($existingReview) {
                $existingReview->update($requestData);

                return ApiResponse::success(new ReviewResource($existingReview), 200);
            } else {
                $review = Review::create($requestData);

                return ApiResponse::success(new ReviewResource($review), 200);
            }
        } catch (\Exception $e) {
            return ApiResponse::error('PS-01'.$e->getMessage(), 400);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $review = Review::where('uuid', $uuid)->firstOrFail();
            $review->update($request->only('rating', 'comment'));

            return new ReviewResource($review);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $review = Review::where('uuid', $uuid)->firstOrFail();
            $review->delete();

            return ApiResponse::success(new ReviewResource($review), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
