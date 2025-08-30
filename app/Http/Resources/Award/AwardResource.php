<?php

namespace App\Http\Resources\Award;

use Illuminate\Http\Resources\Json\JsonResource;

class AwardResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'Awards',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'award_title' => $this->award_title,
            'award_year' => $this->award_year,
            'award_work' => $this->award_work,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
