<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\Job\JobCollection;
use App\Http\Resources\Job\JobResource;
use App\Models\Address;
use App\Models\Category;
use App\Models\Industry;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                    AllowedFilter::scope('country'),
                    AllowedFilter::scope('state'),
                    AllowedFilter::scope('city'),
                    'title',
                    'employement_type',
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

        $jobs = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        $job_collection = new JobCollection($jobs);

        return $job_collection;
    }

    public function store(StoreJobRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $category = Category::where('uuid', $request->category_id)->first();
        $address = Address::where('uuid', $request->address_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'address_id' => $address->id,
            'status' => 0, //pending
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
            $job = Job::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new JobResource($job);
    }

    public function update(UpdateJobRequest $request, $uuid)
    {
        try {
            $job = Job::where('uuid', $uuid)->firstOrFail();
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
}
