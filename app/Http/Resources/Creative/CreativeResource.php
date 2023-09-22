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
            'employment_type' => $this->employment_type,
            'title' => $this->title,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'character_strengths' => getCharacterStrengthNames($this->strengths),

            'priority' => [
                'is_featured' => $this->is_featured,
                'is_urgent' => $this->is_urgent,
            ],
            'workplace_preference' => [
                'is_remote' => $this->is_remote,
                'is_hybrid' => $this->is_hybrid,
                'is_onsite' => $this->is_onsite,
            ],
            'is_opentorelocation' => $this->is_opentorelocation,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
