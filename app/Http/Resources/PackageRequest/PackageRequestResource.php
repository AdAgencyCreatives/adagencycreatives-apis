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
            'category' => $this->category->name,
            'agency_name' => $user->agency->name,
            'contact_name' => $user->full_name,
            'email' => $user->email,
            'phone_number' => $user->phones()->where('label', 'business')->first()?->phone_number,
            'status' => $this->status,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'location' => [
                'state' => $this->state->name,
                'city' => $this->city->name,
            ],
            'comment' => $this->comment,
            'impersonate_url' => route('advisor.impersonate', $user->uuid),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}