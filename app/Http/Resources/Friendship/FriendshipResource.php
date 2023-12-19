<?php

namespace App\Http\Resources\Friendship;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendshipResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();
        $friend = $this->get_friend($user);

        return [
            'title' => $friend->creative?->title,
            'location' => get_location($friend),
            'user' => new UserResource($friend),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }

    public function get_friend($user)
    {
        if ($user->id == $this->user1_id) {
            return $this->receivedByUser;
        } elseif ($user->id == $this->user2_id) {
            return $this->initiatedByUser;
        }

        return null;
    }
}
