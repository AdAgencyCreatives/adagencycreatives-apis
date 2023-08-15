<?php

namespace App\Http\Resources\Experience;

use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'type' => 'experiences',
            'id' => $this->uuid,
            'resume_id' => $this->resume->uuid,
            'title' => $this->title,
            'company' => $this->company,
            'description' => $this->description,
            'started_at' => $this->started_at->format(config('global.datetime_format')),
            'completed_at' => $this->completed_at ? $this->completed_at->format(config('global.datetime_format')) : null,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];

    }
}
