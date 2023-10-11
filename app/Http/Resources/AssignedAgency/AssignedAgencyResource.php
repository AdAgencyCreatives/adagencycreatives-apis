<?php

namespace App\Http\Resources\AssignedAgency;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignedAgencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'user_id' => $this->uuid,
            'agency' => $this->agency->name,
            'impersonate_url' => route('impersonate', $this->id),
        ];
    }
}
