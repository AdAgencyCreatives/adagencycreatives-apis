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
            'email' => $user->email,
            'size' => $this->size,
            'about' => $this->about,
            'role' => $user->role,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'logo' => $user->agency_logo ? getAttachmentBasePath() . $user->agency_logo->path : null,
            'logo_id' => $user->agency_logo ? $user->agency_logo->uuid : null,
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
            'phone_number' => $user->business_phone ? $user->business_phone->phone_number : null,
            'location' => $this->get_location($user),
            'open_jobs' => $user->open_jobs(),
            'links' => new LinkCollection($user->links),
            'seo' => $this->generate_seo(),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
            'featured_at' => $this->featured_at ? $this->featured_at?->format(config('global.datetime_format')) : null,
        ];
    }

    public function get_location($user)
    {
        $address = $user->addresses ? collect($user->addresses)->firstWhere('label', 'business') : null;

        if ($address) {
            return [
                'state_id' => $address->state ? $address->state->uuid : null,
                'state' => $address->state ? $address->state->name : null,
                'city_id' => $address->city ? $address->city->uuid : null,
                'city' => $address->city ? $address->city->name : null,
            ];
        } else {
            return [
                'state_id' => null,
                'state' => null,
                'city_id' => null,
                'city' => null,
            ];
        }
    }

    public function generate_seo()
    {
        $site_name = settings('site_name');
        $separator = settings('separator');

        $seo_title = $this->generateSeoTitle($site_name, $separator);
        $seo_description = $this->generateSeoDescription($site_name, $separator);

        return [
            'title' => $seo_title,
            'description' => $seo_description,
            'tags' => $this->seo_keywords,
        ];
    }

    private function generateSeoTitle($site_name, $separator)
    {
        $seo_title_format = $this->seo_title ? $this->seo_title : settings('agency_title');

        return replacePlaceholders($seo_title_format, [
            '%agencies_contact_first_name%' => $this->user->first_name,
            '%agencies_contact_last_name%' => $this->user->last_name,
            '%agencies_company_name%' => $this->name,
            '%agencies_location%' => isset($this->location) ? sprintf('%s, %s', ($this->location['city'] ? $this->location['city'] : ''), ($this->location['state'] ? $this->location['state'] : '')) : '',
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }

    private function generateSeoDescription($site_name, $separator)
    {
        $seo_description_format = $this->seo_description ? $this->seo_description : settings('agency_description');

        return replacePlaceholders($seo_description_format, [
            '%agencies_about%' => $this->about,
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }
}