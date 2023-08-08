<?php

namespace App\Http\Resources\Agency;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'agencies',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'name' => $this->name,
            'attachment_id' => $this->attachment_id,
            'about' => $this->about,
            'size' => $this->size,
            'type_of_work' => $this->type_of_work,
            'industry_specialty' => $this->industry_specialty,
            'created_at' => $this->created_at->format(config('ad-agency-settings.datetime_format')),
        ];
    }
}
