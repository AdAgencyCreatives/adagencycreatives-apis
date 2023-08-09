<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'jobs',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'employement_type' => $this->employement_type,
            'industry_experience' => $this->industry_experience,
            'media_experience' => $this->media_experience,
            'salary_range' => $this->salary_range,
            'experience' => $this->experience,
            'apply_type' => $this->apply_type,
            'external_link' => $this->external_link,
            'is_remote' => $this->is_remote,
            'is_hybrid' => $this->is_hybrid,
            'is_hybrid' => $this->is_hybrid,
            'is_hybrid' => $this->is_hybrid,
            'created_at' => $this->created_at->format(config('ad-agency-creatives.datetime_format')),
            'updated_at' => $this->created_at->format(config('ad-agency-creatives.datetime_format')),
        ];
    }
}
