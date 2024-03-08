<?php

namespace App\Http\Resources\Reaction;

use Illuminate\Http\Resources\Json\JsonResource;

class PostReactionResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $user->uuid,
            'post_id' => $this->post->uuid,
            'user' => $user->full_name,
            'username' => $user->username,
            'profile_picture' => get_profile_picture($user),
            'reaction_type' => $this->type,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
