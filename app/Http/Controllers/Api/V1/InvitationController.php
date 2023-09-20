<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invitation\StoreInvitationRequest;
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

class InvitationController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(GroupInvitation::class)
            ->allowedFilters([
                AllowedFilter::scope('receiver_id'),
                'status',
            ]);

        $invitations = $query->paginate(config('global.request.pagination_limit'));

        return new InvitationCollection($invitations);
    }

    public function store(StoreInvitationRequest $request)
    {
        $invitee = User::where('uuid', $request->receiver_id)->first();
        $group = Group::where('uuid', $request->group_id)->first();

        if ($group->isMember($invitee)) {
            return ApiResponse::error('User is already a member of the group.', 409);
        }

        $inviter = User::where('uuid', $request->sender_id)->first();

        try {
            $request->merge([
                'uuid' => Str::uuid(),
                'inviter_user_id' => $inviter->id,
                'invitee_user_id' => $invitee->id,
                'group_id' => $group->id,
            ]);

            $invitation = GroupInvitation::create($request->all());

            SendEmailJob::dispatch([
                'receiver' => $invitee,
                'data' => [
                    'recipient' => $invitee->first_name,
                    'inviter' => $invitee->first_name.' '.$invitee->last_name,
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

            return new InvitationResource($invitation);
        } catch (ModelNotFoundException $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
