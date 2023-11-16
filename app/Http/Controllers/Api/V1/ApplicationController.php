<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Application\StoreApplicationRequest;
use App\Http\Requests\Application\UpdateApplicationRequest;
use App\Http\Resources\Application\ApplicationCollection;
use App\Http\Resources\Application\ApplicationResource;
use App\Http\Resources\AppliedJob\AppliedJobCollection;
use App\Jobs\SendEmailJob;
use App\Models\Application;
use App\Models\Attachment;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Application::class)
            ->allowedFilters([
                AllowedFilter::scope('user_id'),
                AllowedFilter::scope('job_id'),
                'status',
            ]);

        $applications = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ApplicationCollection($applications);
    }

    public function store(StoreApplicationRequest $request)
    {
        $applicant_user = User::where('uuid', $request->user_id)->first();
        $job = Job::where('uuid', $request->job_id)->first();
        $attachment = Attachment::where('uuid', $request->resume_id)->first();

        $agency_user = $job->user;

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $applicant_user->id,
            'job_id' => $job->id,
            'attachment_id' => $attachment->id ?? null,
            'status' => 0,
        ]);

        try {
            $application = Application::create($request->all());

            SendEmailJob::dispatch([
                'receiver' => $applicant_user,
                'data' => [
                    'recipient' => $applicant_user->first_name,
                    'job_title' => $job->title,
                ],
            ], 'application_submitted');

            $resume_url = "";
            if (isset($applicant_user->resume)) {
                $resume_url = getAttachmentBasePath() . $applicant_user->resume->path;
            } else {
                $resume_url = route('download.resume', $applicant_user->uuid);
            }

            SendEmailJob::dispatch([
                'receiver' => $agency_user,
                'data' => [
                    'applicant' => $applicant_user,
                    'job_title' => $job->title,
                    'job_url' => sprintf("%s/job/%s", env('FRONTEND_URL'), $job->slug),
                    'resume_url' => $resume_url,
                    'creative_profile' => sprintf("%s/creative/%s", env('FRONTEND_URL'), $applicant_user->username),
                    'message' => $request->message
                ],
            ], 'new_candidate_application');

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

    public function applied_jobs(Request $request)
    {
        $user = $request->user();

        $applications = Application::with('job')
            ->where('user_id', $user->id)
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new AppliedJobCollection($applications);
    }
}