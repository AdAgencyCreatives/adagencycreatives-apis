<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobInvitationRequest;
use App\Jobs\SendEmailJob;
use App\Models\Api\V1\JobInvitation;
use App\Models\Job;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class JobInvitationController extends Controller
{
    public function job_invitation(StoreJobInvitationRequest $request)
    {
        $agency_user = $request->user();
        $invitee_user = User::where('uuid', $request->receiver_id)->first();
        $job = Job::with('user.agency')->where('uuid', $request->job_id)->first();

        // Check if the invitation already exists
        $existingInvitation = JobInvitation::where([
            'user_id' => $agency_user->id,
            'creative_id' => $invitee_user->id,
            'job_id' => $job->id,
        ])->first();

        //If the invitation already exists, show an appropriate message
        if ($existingInvitation) {
            return response()->json([
                'message' => 'Invitation already sent to this user for this job.',
            ]);
        }

        // Record the notification in the job_invitations table
        $job_invitation = JobInvitation::create([
            'uuid' => Str::uuid(),
            'user_id' => $agency_user->id,
            'creative_id' => $invitee_user->id,
            'job_id' => $job->id,
            'status' => 'pending',
        ]);

        try {
            SendEmailJob::dispatch([
                'receiver' => $invitee_user,
                'data' => [
                    'receiver_name' => $invitee_user->first_name,
                    'agency_name' => $job?->agency_name ?? ($agency_user?->agency?->name ?? ''),
                    'job_title' => $job->title,
                    'job_url' => route('job.inviatation.status.update', ['uuid' => $job_invitation->uuid]),
                ],
            ], 'job_invitation');

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);

            //Also send this as a message in Job Messages, so that both can send/receive messages
            Message::create([
                'uuid' => Str::uuid(),
                'sender_id' => $agency_user->id,
                'receiver_id' => $invitee_user->id,
                'message' => sprintf("Job Invitation for <a style='text-decoration:underline;' href='%s'>%s</a></b>", $job_url, $job->title),
                'type' => "job",
            ]);

            return response()->json([
                'message' => 'Job invitation sent successfully',
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function update_job_invitation_status(Request $request, $uuid)
    {
        $job_invitation = JobInvitation::where('uuid', $uuid)->first();
        $job_invitation->touch('read_at');

        $job = Job::where('id', $job_invitation->job_id)->first();
        $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
        // Redirect the user to the desired job link
        return redirect()->away($job_url);
    }
}