<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\Job\JobCollection;
use App\Http\Resources\Job\JobResource;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::paginate(config('ad-agency-creatives.request.pagination_limit'));
        return new JobCollection($jobs);
    }

    public function store(StoreJobRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'address_id' => 1,
            'status' => 0,
        ]);
        try {
            $job = Job::create($request->all());
            return ApiResponse::success(new JobResource($job), 200);

        } catch (\Exception $e) {
            return ApiResponse::error('JS-01' . $e->getMessage(), 400);
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

            return ApiResponse::success( new JobResource($job), 200);

        } catch (\Exception $exception) {

            return ApiResponse::error(trans('response.not_found'), 404);

        }
    }
}