<?php

namespace App\Http\Resources\Like;

use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $user->uuid,
            'post_id' => $this->post->uuid,
            'user' => $user->first_name.' '.$user->last_name,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
