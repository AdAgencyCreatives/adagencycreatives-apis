<?php

namespace App\Http\Resources\Education;

use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'educations',
            'id' => $this->uuid,
            'resume_id' => $this->resume->uuid,
            'degree' => $this->degree,
            'college' => $this->college,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];

    }
}
