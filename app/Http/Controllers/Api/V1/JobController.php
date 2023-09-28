<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\Job\JobCollection;
use App\Http\Resources\Job\JobResource;
use App\Models\Category;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->all();

        $industries = $this->processExperience($request, $filters, 'industry_experience');
        $medias = $this->processExperience($request, $filters, 'media_experience');

        $query = QueryBuilder::for(Job::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('category_id'),
                AllowedFilter::scope('industry_experience'),
                AllowedFilter::scope('state_id'),
                AllowedFilter::scope('city'),
                'title',
                'employment_type',
                'apply_type',
                'salary_range',
                'is_remote',
                'is_hybrid',
                'is_onsite',
                'is_featured',
                'is_urgent',
                'status',
            ])
            ->allowedSorts('created_at');

        if ($industries !== null) {
            $query->whereIn('industry_experience', $industries);
        }
        if ($medias !== null) {
            $query->whereIn('media_experience', $medias);
        }

        $jobs = $query->with('user.agency', 'category', 'state', 'city', 'attachment')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        $job_collection = new JobCollection($jobs);

        return $job_collection;
    }

    public function store(StoreJobRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $category = Category::where('uuid', $request->category_id)->first();
        $state = Location::where('uuid', $request->state_id)->first();
        $city = Location::where('uuid', $request->city_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'state_id' => $state->id ?? null,
            'city_id' => $city->id ?? null,
            'status' => 'draft',
            'industry_experience' => ''.implode(',', $request->industry_experience).'',
            'media_experience' => ''.implode(',', $request->media_experience).'',
        ]);

        try {
            $job = Job::create($request->all());

            return ApiResponse::success(new JobResource($job), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('JS-01'.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $job = Job::with('user.agency', 'attachment')->where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

            // return $job;
        return new JobResource($job);
    }

    public function updateFromAdmin(UpdateJobRequest $request, $uuid)
    {
        // dd($request->all());
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();

            $category = Category::where('uuid', $request->category_id)->first();
            $request->merge([
                'category_id' => $category->id,
                'industry_experience' => ''.implode(',', $request->industry_experience).'',
                'media_experience' => ''.implode(',', $request->media_experience).'',
            ]);

            $job->update($request->all());

            return new JobResource($job);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function update(Request $request, $uuid)
    {

        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();

            $oldStatus = $job->status;
            $newStatus = $request->input('status');

            if ($newStatus === 'published' && $oldStatus === 'draft') {
                $user = Auth::user();
                if (! $user) {
                    return ApiResponse::error(trans('response.unauthorized'), 401);
                }

                $subscription = Subscription::where('user_id', $user->id)
                    ->where('stripe_status', 'active')
                    ->where('quota_left', '>', 0)
                    ->first();

                if (! $subscription) {
                    return ApiResponse::error("You don't have enough quota for this job", 402);
                }

                $subscription->decrement('quota_left', 1);
                $newStatus = 'pending';
            }
            $job->update($request->all());

            return new JobResource($job);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();
            $job->delete();

            return ApiResponse::success(new JobResource($job), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function processExperience(Request $request, &$filters, $experienceKey)
    {
        if (isset($filters['filter'][$experienceKey])) {
            $experience_ids = $filters['filter'][$experienceKey];
            unset($filters['filter'][$experienceKey]);
            $request->replace($filters);

            if ($experience_ids) {
                $experience_ids = explode(',', $experience_ids);
            } else {
                $experience_ids = [];
            }

            return Industry::whereIn('uuid', $experience_ids)->pluck('id');
        }

        return null;
    }

    public function get_employment_types()
    {
        $cacheKey = 'employment_types';
        $users = Cache::remember($cacheKey, now()->addMinutes(120), function () {
            return Job::EMPLOYMENT_TYPE;
        });

        return $users;
    }
}
