<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Http\Requests\GroupMember\StoreGroupMemberRequest;
use App\Http\Requests\GroupMember\UpdateGroupMemberRequest;
use App\Http\Resources\GroupMember\GroupMemberCollection;
use App\Http\Resources\GroupMember\GroupMemberResource;
use App\Models\Group;
use App\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class GroupMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(GroupMember::class)
            ->allowedFilters([
                AllowedFilter::scope('group_id'),
                'role'
            ]);

        $group_members = $query->paginate($request->per_page ?? config('global.request.pagination_limit'));

        return new GroupMemberCollection($group_members);
    }


    public function create()
    {
        //
    }


    public function store(StoreGroupMemberRequest $request)
    {
        $user = User::where('uuid', $request->user_id)->firstOrFail();
        $group = Group::where('uuid', $request->group_id)->firstOrFail();

        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User is already a member of the group.'], 422);
        }

        $group_member = GroupMember::create([
            'uuid' => Str::uuid(),
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'joined_at' => now(),
        ]);

        return new GroupMemberResource($group_member);
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


    public function destroy(Request $request, $uuid)
    {
        $group = Group::where('uuid', $request->group_id)->firstOrFail();
        $user = User::where('uuid', $uuid)->firstOrFail();

        try {
            $group_member = GroupMember::where('user_id', $user->id)->where('group_id', $group->id)->firstOrFail();
            $group_member->delete();

            return ApiResponse::success(new GroupMemberResource($group_member), 200);
        } catch (\Exception $exception) {
            return ApiResponse::error(trans('response.not_found'), 404);
        }
    }
}