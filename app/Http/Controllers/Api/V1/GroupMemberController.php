<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupMember\StoreGroupMemberRequest;
use App\Http\Requests\GroupMember\UpdateGroupMemberRequest;
use App\Http\Resources\GroupInvitation\InvitationResource;
use App\Http\Resources\GroupMember\GroupMemberCollection;
use App\Http\Resources\GroupMember\GroupMemberResource;
use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(GroupMember::class)
            ->allowedFilters([
                AllowedFilter::scope('group_id'),
                AllowedFilter::scope('user_id'),
                'role',
            ])
            ->allowedSorts('created_at');

        $group_members = $query
        ->with('user.creative')
        ->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new GroupMemberCollection($group_members);
    }

    public function store(StoreGroupMemberRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->firstOrFail();
        $group = Group::where('uuid', $request->group_id)->firstOrFail();

        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already a member of the group.'], 422);
        }

        if($group->status == 'private') {

            if(GroupInvitation::where('invitee_user_id', $user->id)->where('group_id', $group->id)->exists()) {
                return response()->json(['message' => 'Group invitation already sent.'], 422);
            }
            $group_invitation = GroupInvitation::create([
                'uuid' => Str::uuid(),
                'inviter_user_id' => $user->id,
                'invitee_user_id' => $user->id,
                'group_id' => $group->id,
        ]);

            return new InvitationResource($group_invitation);
        } else {
            $group_member = GroupMember::create([
            'uuid' => Str::uuid(),
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'joined_at' => now(),
        ]);
            return new GroupMemberResource($group_member);
        }
    }

    public function update(UpdateGroupMemberRequest $request, $uuid)
    {
        $group = Group::where('uuid', $request->group_id)->firstOrFail();
        $user = User::where('uuid', $uuid)->firstOrFail();

        $group_member = GroupMember::where('user_id', $user->id)
            ->where('group_id', $group->id)->update(
                $request->only('role')
            );

        $group_member = GroupMember::where('user_id', $user->id)
            ->where('group_id', $group->id)->first();

        return new GroupMemberResource($group_member);
    }

    public function leave_membership(Request $request)
    {
        $group = Group::where('uuid', $request->group_id)->first();
        $user = User::where('uuid', $request->receiver_id)->first();

        $deleted_1 = false;
        $deleted_2 = false;
        try {
            if($group->status != 0) { //if group in private then delete its invitation also
                $group_invitation = GroupInvitation::where('group_id', $group->id)->where('invitee_user_id', $user->id)->first();
                if($group_invitation){
                    if($group_invitation->status == 1){
                        $group_invitation->delete();
                        $deleted_1 = true;
                    }
                }
            }

            $group_member = GroupMember::where('user_id', $user->id)->where('group_id', $group->id)->first();
            if($group_member){
                $group_member->delete();
                $deleted_2 = true;
            }
            if($deleted_1 || $deleted_2){
                return "true";
            }
            return "false";

        } catch (\Exception $e) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}
