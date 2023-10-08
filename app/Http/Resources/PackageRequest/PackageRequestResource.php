<?php

namespace App\Http\Resources\PackageRequest;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageRequestResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'type' => 'package_requests',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'title' => $this->title,
            'agency_name' => $this->agency_name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'location' => [
                'state' => $this->state->name,
                'city' => $this->city->name,
            ],
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
