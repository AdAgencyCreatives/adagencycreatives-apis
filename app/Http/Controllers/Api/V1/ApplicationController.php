<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MessageReceived;
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
use App\Models\Message;
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

        $query = $query->with('job');
        $applications = $query->orderBy('status', 'asc')->orderBy('id', 'desc')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ApplicationCollection($applications);
    }

    public function store(StoreApplicationRequest $request)
    {
        $applicant_user = User::where('uuid', $request->user_id)->first();
        $job = Job::where('uuid', $request->job_id)->first();
        // $attachment = Attachment::where('uuid', $request->resume_id)->first();

        $existingApplication = Application::where('user_id', $applicant_user->id)
            ->where('job_id', $job->id)
            ->first();

        if ($existingApplication) {
            return ApiResponse::error('You have already applied for this job.', 400);
        }

        $agency_user = $job->user;

        $request->merge([
            'uuid' => Str::uuid(),
            'user_id' => $applicant_user->id,
            'job_id' => $job->id,
            // 'attachment_id' => $attachment->id ?? null,
            'status' => 0,
        ]);

        $event_data1 = [
            'receiver_id' => $applicant_user->uuid,
            'message_sender_id' => $applicant_user->uuid,
            'message_receiver_id' => $agency_user->uuid,
            'message' => 'You applied on a job posted by ' . $agency_user->full_name,
            'message_type' => 'conversation_updated',
            'message_action' => 'message-received'
        ];

        $event_data2 = [
            'receiver_id' => $agency_user->uuid,
            'message_sender_id' => $applicant_user->uuid,
            'message_receiver_id' => $agency_user->uuid,
            'message' => $applicant_user->full_name . ' applied on a job posted by you',
            'message_type' => 'conversation_updated',
            'message_action' => 'message-sent'
        ];

        try {
            $application = Application::create($request->all());

            /**
             * only send email to creative user if they apply on internal jobs,
             * and if they aply on external jobs then we will not know whether
             * they really appplied or not
             */

            if ($job->apply_type == 'Internal') {
                SendEmailJob::dispatch([
                    'receiver' => $applicant_user,
                    'data' => [
                        'recipient' => $applicant_user->first_name,
                        'job_title' => $job->title,
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                    ],
                ], 'application_submitted');
            }


            //Also send this as a message in Job Messages, so that both can send/receive messages
            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
            $creative_url = sprintf('%s/creative/%s', env('FRONTEND_URL'), $applicant_user->username);
            $msg_data = [
                'uuid' => Str::uuid(),
                'sender_id' => $applicant_user->id,
                'receiver_id' => $agency_user->id,
                'message' => sprintf(
                    "<a style='text-decoration:underline;' href='%s'>%s</a> applied on the job <a style='text-decoration:underline;' href='%s'>%s</a>",
                    $creative_url,
                    $applicant_user->full_name,
                    $job_url,
                    $job->title
                ),
                'type' => "job",
                'created_at' => now(),
            ];

            if ($job->apply_type == 'External') {
                $msg_data['message'] = sprintf(
                    "<a style='text-decoration:underline;' href='%s'>%s</a> is interested in the job <a style='text-decoration:underline;' href='%s'>%s</a>",
                    $creative_url,
                    $applicant_user->full_name,
                    $job_url,
                    $job->title
                );
            }

            /**
             * If job is submitted by advisor, then send email only to advisor and do not
             * bother agency member with bunch of emails
             */
            if ($job->advisor_id) {
                $advisor_user = User::find($job->advisor_id);

                $resume_url = $this->get_resume_url($applicant_user, $applicant_user);

                SendEmailJob::dispatch([
                    'receiver' => $advisor_user,
                    'data' => [
                        'receiver_name' => $advisor_user->first_name ?? $advisor_user->username,
                        'applicant' => $applicant_user,
                        'job_title' => $job->title,
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                        'resume_url' => $resume_url,
                        'creative_name' => sprintf('%s %s', $applicant_user->first_name, $applicant_user->last_name),
                        'creative_profile' => sprintf('%s/creative/%s', env('FRONTEND_URL'), $applicant_user->username),
                        'message' => $request->message,
                    ],
                ], 'new_candidate_application'); // To the agency

                $msg_data['receiver_id'] = $advisor_user->id;
                $event_data2["receiver_id"] = $advisor_user->uuid;
            }

            Message::create($msg_data);
            if ($job->apply_type == 'Internal' && $request->message != '') {
                $seconds = now()->addSecond();
                $msg_data['created_at'] = $seconds;
                $msg_data['updated_at'] = $seconds;
                $msg_data['message'] = $request->message;
                Message::create($msg_data);
            }

            event(new MessageReceived($event_data1));
            event(new MessageReceived($event_data2));

            return ApiResponse::success(new ApplicationResource($application), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('AS-01' . $e->getMessage(), 400);
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
            ->whereHas('job')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new AppliedJobCollection($applications);
    }

    public function get_resume_url($user, $logged_in_user)
    {
        if (isset($user->resume)) {
            return getAttachmentBasePath() . $user->resume->path;
        } else {
            $resume_filename = sprintf('%s_%s_AdAgencyCreatives_%s', $user->first_name, $user->last_name, date('Y-m-d'));

            return route('download.resume', ['name' => $resume_filename, 'u1' => $user->uuid, 'u2' => $logged_in_user?->uuid]);
        }
    }

    public function remove_from_recent($uuid)
    {
        try {
            return $uuid;
            $application = Application::where('uuid', $uuid)->firstOrFail();
            $application->update(['remove_from_recent' => true]);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new ApplicationResource($application);
    }
}