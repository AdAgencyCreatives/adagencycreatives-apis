<?php

namespace App\Http\Resources\GroupMember;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{

    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->uuid,
            'role' => $this->role,
            'user' => new UserResource($user),
            'joined_at' => $this->joined_at->format(config('global.datetime_format')),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}