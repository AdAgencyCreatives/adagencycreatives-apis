<?php

namespace App\Http\Resources\Resume;

use Illuminate\Http\Resources\Json\JsonResource;

class ResumeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'resumes',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'years_of_experience' => $this->years_of_experience,
            'about' => $this->about,
            'industry_specialty' => $this->industry_specialty,
            'media_experience' => $this->media_experience,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
