<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupInvitation\InvitationCollection;
use App\Http\Resources\GroupInvitation\InvitationResource;
use App\Jobs\SendEmailJob;
use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupInvitationController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(GroupInvitation::class)
            ->allowedFilters([
                AllowedFilter::scope('group_id'),
                AllowedFilter::scope('sender_id'),
                AllowedFilter::scope('receiver_id'),
                AllowedFilter::scope('sent'),
                'status',
            ]);

        $invitations = $query->paginate(config('global.request.pagination_limit'));

        return new InvitationCollection($invitations);
    }

    public function store(Request $request)
    {
        $invitee = User::where('uuid', $request->receiver_id)->first(); // To whom email was sent
        $group = Group::where('uuid', $request->group_id)->first();
        $sent = isset($request->sent) ? 1 : 0;
        
        if ($group->isMember($invitee)) {
            return ApiResponse::error('User is already a member of the group.', 409);
        }

        $inviter = $request->user(); //Sender

        if ($group->isInvitationAlreadySent($invitee)) {
            return ApiResponse::error(sprintf('You already sent invitation to %s %s', $invitee->first_name, $invitee->last_name), 409);
        }

        try {
            $request->merge([
                'uuid' => Str::uuid(),
                'inviter_user_id' => $inviter->id,
                'invitee_user_id' => $invitee->id,
                'group_id' => $group->id,
                '$sent' => $sent
            ]);

            $invitation = GroupInvitation::create($request->all());

            if ($inviter->role == 'creative') {
                $inviter_profile_url = sprintf('%s/creative/%s', env('FRONTEND_URL'), $inviter->username);
            } elseif ($inviter->role == 'agency') {
                $inviter_profile_url = sprintf('%s/agency/%s', env('FRONTEND_URL'), $inviter->agency?->slug);
            }
            
            $action_url = sprintf('%s/groups/%s/#invite=%s', env('FRONTEND_URL'), $group->uuid, $invitation->uuid);

            SendEmailJob::dispatch([
                'receiver' => $invitee,
                'data' => [
                    'recipient' => $invitee->first_name,
                    'inviter' => $inviter->first_name.' '.$inviter->last_name,
                    'inviter_profile_url' => $inviter_profile_url ?? '#',
                    'action_url' => $action_url ?? '#',
                    'group' => $group->name,
                ],
            ], 'group_invitation');

            return new InvitationResource($invitation);
        } catch (\Exception $e) {
            throw new ApiException($e, 'CS-01');
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $invitation = GroupInvitation::where('uuid', $uuid)->first();
            $invitation->update($request->only('status'));

            $invitation->touch('accepted_at');

            return new InvitationResource($invitation);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}