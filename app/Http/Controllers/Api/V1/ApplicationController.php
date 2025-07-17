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
use App\Models\Creative;
use App\Models\Job;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

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

        $recent_only = $request->has('recent_only') && $request->recent_only == "yes";

        if ($recent_only) {
            $query->where('status', 0);
        }

        $query->with('job', function ($q) use ($recent_only) {
            if ($recent_only) {
                $q->where('status', 1);
            }
        });

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

            if ($job->apply_type == 'Internal' && $applicant_user?->email_notifications_enabled) {
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
                    "<a style='text-decoration:underline;' href='%s'>%s</a> clicked apply now to the job <a style='text-decoration:underline;' href='%s'>%s</a>",
                    $creative_url,
                    $applicant_user->full_name,
                    $job_url,
                    $job->title
                );
            }

            $job_user = $job->user;
            /**
             * If job is submitted by advisor, then send email only to advisor and do not
             * bother agency member with bunch of emails
             */
            if ($job->advisor_id) {
                $job_user = User::find($job->advisor_id);
            }

            $resume_url = $this->get_resume_url($applicant_user, $applicant_user);

            if ($job_user?->email_notifications_enabled) {
                // send email only if job notifications are enabled.
                SendEmailJob::dispatch([
                    'receiver' => $job_user,
                    'data' => [
                        'receiver_name' => $job_user->first_name ?? $job_user->username,
                        'applicant' => $applicant_user,
                        'job_title' => $job->title,
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                        'resume_url' => sprintf('%s/creative/resume/%s', env('FRONTEND_URL'), $applicant_user->username),
                        'creative_name' => sprintf('%s %s', $applicant_user->first_name, $applicant_user->last_name),
                        'creative_profile' => sprintf('%s/creative/%s', env('FRONTEND_URL'), $applicant_user->username),
                        'creative_aac_profile' => sprintf('%s/creative-pdf/%s', env('FRONTEND_URL'), $applicant_user->username),
                        'message' => $request->message,
                        'apply_type' => $job->apply_type,
                    ],
                ], 'new_candidate_application'); // To the agency
            }

            $msg_data['receiver_id'] = $job_user->id;
            $event_data2["receiver_id"] = $job_user->uuid;

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
            $application->refresh();

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
        $searchText = $request->searchText;

        $applications = Application::with('job')
            ->whereHas('job', function ($query) use ($searchText) {
                $query
                    ->where('title', 'LIKE', '%' . $searchText . '%')
                    ->orWhereHas('user.agency', function ($q) use ($searchText) {
                        $q->where('name', 'LIKE', '%' . $searchText . '%');
                    });
            })
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

    public function remove_from_recent(Request $request, $uuid)
    {
        try {

            $user = User::where('uuid', $request->user_id)->firstOrFail();
            $user_id = $user->id;

            $application = Application::where('uuid', $uuid)->firstOrFail();

            $existing_users = $application->removed_from_recent ?? "";
            $new_users = $user_id;

            if (strlen($existing_users) > 0) {
                $users_arr = preg_split("/,/", $existing_users);
                if (!in_array($user_id, $users_arr)) {
                    $users_arr[count($users_arr)] = $user_id;
                    $new_users = join(",", $users_arr);
                } else {
                    $new_users = $existing_users;
                }
            }

            $application->update(['removed_from_recent' => $new_users]);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }

        return new ApplicationResource($application);
    }

    public function get_creative_applications(Request $request)
    {
        $job_user = User::where('uuid', '=', $request->job_user_id)->first();
        $job_user_id = $job_user->id;

        $creative = Creative::where('uuid', '=', $request->creative_user_id)->first();
        $creative_user_id = $creative->user->id;

        $query = Application::whereHas('job.user', function ($q) use ($job_user_id) {
            $q->where('id', '=', $job_user_id);
        })->where('user_id', '=', $creative_user_id);

        $applications = $query->orderBy('status', 'asc')->orderBy('id', 'desc')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ApplicationCollection($applications);
    }

    public function is_creative_applicant(Request $request)
    {
        $job_user = User::where('uuid', '=', $request->job_user_id)->first();
        $job_user_id = $job_user->id;

        $creative_user = User::where('uuid', '=', $request->creative_user_id)->first();
        $creative_user_id = $creative_user->id;

        $job_ids = Job::where('user_id', '=', $job_user_id)->orWhere('advisor_id', '=', $job_user_id)->pluck('id')->toArray();
        $app_ids = Application::whereIn('job_id', $job_ids)->where('user_id', '=', $creative_user_id)->pluck('id')->toArray();

        return count($app_ids) > 0;
    }

    public function get_all_applications(Request $request)
    {
        $query = QueryBuilder::for(Application::class)
            ->allowedFilters([
                AllowedFilter::scope('job_id'),
                'status',
            ]);

        $job_user = User::where('uuid', $request->job_user_id)->first();
        $job_ids = Job::where('user_id', $job_user->id)->pluck('id')->toArray();
        $query->whereIn('job_id', $job_ids);

        if (!empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->whereRaw("(CONCAT(users.first_name, ' ', users.last_name) LIKE '%" . trim($search) . "%')");
            });
        }

        $query->whereExists(function ($query) {
            $query->select(DB::raw(1))->from('users')->whereColumn('users.id', 'applications.user_id');
        })->whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        });


        $applications = $query->orderBy('status', 'asc')->orderBy('id', 'desc')->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new ApplicationCollection($applications);
    }
}
