<?php

namespace App\Http\Resources\Job;

use App\Http\Resources\Address\AddressResource;
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
            'category' => $this->category->name,
            'employement_type' => $this->employement_type,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getIndustryNames($this->media_experience),
            'salary_range' => $this->salary_range,
            'experience' => $this->experience,
            'apply_type' => $this->apply_type,
            'external_link' => $this->external_link,
            'is_remote' => $this->is_remote,
            'is_hybrid' => $this->is_hybrid,
            'is_onsite' => $this->is_onsite,
            'is_featured' => $this->is_featured,
            'is_urgent' => $this->is_urgent,
            'address' => new AddressResource($this->address),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
