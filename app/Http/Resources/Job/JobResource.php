<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type' => 'jobs',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category->name,
            'employment_type' => $this->employment_type,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'character_strengths' => getCharacterStrengthNames($this->strengths),
            'salary_range' => $this->salary_range,
            'experience' => $this->years_of_experience,
            'apply_type' => $this->apply_type,
            'external_link' => $this->external_link,
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
            'is_opentoremote' => $this->is_opentoremote,
            'status' => $this->status,
            'location' => [
                'state' => $this->state->name,
                'city' => $this->city->name,
            ],
            'agency' => [],
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'expired_at' => $this->expired_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];

        $agency = $this->user->agency;
        if ($agency) {
            if ($this->agency_name == null) {
                $data['agency'] = [
                    'name' => $agency->name,
                    'logo' => $agency->attachment ? $agency->attachment->path : null,
                ];

            } else {
                $data['agency'] = [
                    'name' => $this->agency_name,
                    'logo' => $this->attachment ? getAttachmentBasePath().$this->attachment->path : null,
                ];
            }
        }

        return $data;
    }
}
