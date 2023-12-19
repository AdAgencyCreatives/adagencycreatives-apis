<?php

namespace App\Http\Resources\Friendship;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendshipRequestResource extends JsonResource
{
    public function toArray($request)
    {
        $logged_in_user = $request->user();

        $other_user = null;

        if ($logged_in_user->id == $this->sender->id) {
            $other_user = $this->receiver;
        } elseif ($logged_in_user->id == $this->receiver->id) {
            $other_user = $this->sender;
        }

        if ($other_user !== null) {
            return [
                'id' => $this->uuid,
                'title' => $other_user->creative?->title,
                'location' => get_location($other_user),
                'user' => new UserResource($other_user),
                'status' => $this->status,
                'created_at' => $this->created_at->format(config('global.datetime_format')),
                'updated_at' => $this->created_at->format(config('global.datetime_format')),
            ];
        }

        return [];
    }
}
