<?php

namespace App\Http\Resources\Creative;

use Illuminate\Http\Resources\Json\JsonResource;

class CreativeResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'type' => 'creatives',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'name' => $this->user->first_name.' '.$this->user->last_name,
            'title' => isset($this->category) ? $this->category->name : null,
            'profile_image' => $this->get_profile_image($user),
            'years_of_experience' => $this->years_of_experience,
            'about' => $this->about,
            'employment_type' => $this->employment_type,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'character_strengths' => getCharacterStrengthNames($this->strengths),
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
            'location' => $this->get_location($user),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

            'seo' => [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
                'tags' => $this->seo_keywords,
            ],
        ];
    }

    public function get_profile_image($user)
    {
        return isset($user->profile_picture) ? getAttachmentBasePath().$user->profile_picture->path : null;
    }

    public function get_location($user)
    {
        $address = collect($user->addresses)->firstWhere('label', 'personal');

        return $address ? [
            'state' => $address->state->name,
            'city' => $address->city->name,
        ] : null;
    }
}
