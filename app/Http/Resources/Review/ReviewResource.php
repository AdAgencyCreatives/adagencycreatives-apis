<?php

namespace App\Http\Resources\Review;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $user->uuid,
            'user' => $user->first_name.' '.$user->last_name,
            'target_id' => $this->target->uuid,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
