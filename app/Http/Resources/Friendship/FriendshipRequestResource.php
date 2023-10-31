<?php

namespace App\Http\Resources\Friendship;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendshipRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'sender' => new UserResource($this->sender),
            'receiver' => new UserResource($this->receiver),
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
