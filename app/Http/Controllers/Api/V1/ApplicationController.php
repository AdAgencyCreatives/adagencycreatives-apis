<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Http\Resources\Application\ApplicationCollection;
use App\Http\Resources\Application\ApplicationResource;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::paginate(config('global.request.pagination_limit'));

        return new ApplicationCollection($applications);
    }

    public function store(StoreApplicationRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->first();
        $job = Job::where('uuid', $request->job_id)->first();

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'job_id' => $job->id,
            'attachment_id' => $job->id,
            'status' => 0,
        ]);
        try {
            $application = Application::create($request->all());

            return ApiResponse::success(new ApplicationResource($application), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('AS-01'.$e->getMessage(), 400);
        }
    }

    public function show($uuid)
    {
        try {
            $application = Application::where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new ApplicationResource($application);
    }

    public function update(UpdateApplicationRequest $request, $uuid)
    {
        try {
            $application = Application::where('uuid', $uuid)->firstOrFail();
            $application->update($request->only(['status', 'message']));

            return new ApplicationResource($application);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }

    public function destroy($uuid)
    {
        try {
            $application = Application::where('uuid', $uuid)->firstOrFail();
            $application->delete();

            return ApiResponse::success(new ApplicationResource($application), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
