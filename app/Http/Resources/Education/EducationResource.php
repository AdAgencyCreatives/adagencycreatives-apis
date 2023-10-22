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
            'user_id' => $this->user->uuid,
            'degree' => $this->degree,
            'college' => $this->college,
            'completed_at' => $this->completed_at?->format(config('global.datetime_format')),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
