<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\StoreJobInvitationRequest;
use App\Jobs\SendEmailJob;
use App\Models\Api\V1\JobInvitation;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Str;

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

        // If the invitation already exists, show an appropriate message
        if ($existingInvitation) {
            return response()->json([
                'message' => 'Invitation already sent to this user for this job.',
            ]);
        }

        // Record the notification in the job_invitations table
        JobInvitation::create([
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
                    'agency_name' => $agency_user?->agency?->name ?? '',
                    'job_title' => $job->title,
                    'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                ],
            ], 'job_invitation');

            return response()->json([
                'message' => 'Job invitation sent successfully',
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }
}
