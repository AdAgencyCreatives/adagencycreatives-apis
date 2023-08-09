<?php

namespace App\Http\Resources\Creative;

use Illuminate\Http\Resources\Json\JsonResource;

class CreativeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'creatives',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'profile_image' => $this->attachment_id,
            'years_of_experience' => $this->years_of_experience,
            'type_of_work' => $this->type_of_work,
            'created_at' => $this->created_at->format(config('ad-agency-settings.datetime_format')),
        ];
    }
}
