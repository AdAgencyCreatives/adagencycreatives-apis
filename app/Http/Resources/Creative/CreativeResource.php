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
            'years_of_experience' => $this->years_of_experience,
            'about' => $this->about,
            'type_of_work' => $this->type_of_work,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
            'is_opentoremote' => $this->is_opentoremote,
            'is_opentorelocation' => $this->is_opentorelocation,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}