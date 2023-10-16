<?php

namespace App\Http\Resources\Agency;

use App\Http\Resources\Link\LinkCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'type' => 'agencies',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'slug' => $this->slug,
            'name' => $this->name,
            'size' => $this->size,
            'about' => $this->about,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'logo' => $this->attachment ? getAttachmentBasePath().$this->attachment->path : null,
            'is_remote' => $this->is_remote,
            'is_hybrid' => $this->is_hybrid,
            'is_onsite' => $this->is_onsite,
            'priority' => [
                'is_featured' => $this->is_featured,
                'is_urgent' => $this->is_urgent,
            ],
            'workplace_preference' => [
                'is_remote' => $this->is_remote,
                'is_hybrid' => $this->is_hybrid,
                'is_onsite' => $this->is_onsite,
            ],
            'location' => $this->get_location($user),
            'open_jobs' => $user->open_jobs(),
            'links' => new LinkCollection($user->links),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }

    public function get_location($user)
    {
        $address = collect($user->addresses)->firstWhere('label', 'business');

        return $address ? [
            'state' => $address->state->name,
            'city' => $address->city->name,
        ] : null;
    }
}
