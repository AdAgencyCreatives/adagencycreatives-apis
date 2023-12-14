<?php

namespace App\Http\Resources\AssignedAgency;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignedAgencyResource extends JsonResource
{
    public function toArray($request)
    {
        $agency = $this->agency;
        $user = $this->user;

        return [
            'id' => $this->uuid,
            'agency_name' => $agency->name,
            'logo' => get_profile_picture($user),
            'impersonate_url' => route('advisor.impersonate', $user->uuid),
        ];
    }
}