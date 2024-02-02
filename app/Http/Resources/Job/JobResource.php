<?php

namespace App\Http\Resources\Job;

use App\Http\Resources\Application\ApplicationCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray($request)
    {

        $user = $this->user;
        $category = $this->category;
        $applications = $this->applications;

        if($this->advisor_id != null){
            // If application_status is provided, filter applications by status
            if ($request->has('application_status')) {
                $applications = $applications->where('status', $request->application_status);
            }
        }


        $data = [
            'type' => 'jobs',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $category?->uuid,
            'category' => $category?->name,
            'employment_type' => $this->employment_type,
            'industry_experience' => getIndustryNames($this->industry_experience),
            'media_experience' => getMediaNames($this->media_experience),
            'character_strengths' => getCharacterStrengthNames($this->strengths),
            'salary_range' => $this->salary_range,
            'experience' => $this->years_of_experience,
            'apply_type' => $this->apply_type,
            'external_link' => $this->external_link,
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
            'is_opentoremote' => $this->is_opentoremote,
            'status' => $this->status,
            'location' => $this->get_location(),
            'agency' => [],
            'advisor_id' => $this->advisor_id ?? null,
            'seo' => $this->generate_seo(),
            'applications_count' => $this->applications_count,
            'applications' => new ApplicationCollection($applications),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'expired_at' => $this->expired_at?->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];

        $agency = $user->agency;
        if ($agency) {
            if ($this->agency_name == null) {
                $data['agency']['name'] = $agency->name;

            } else {
                $data['agency']['name'] = $this->agency_name;
            }

            $sub_agency_logo = get_agency_logo($this, $user);
            $data['agency']['logo'] = getAttachmentBasePath() . $sub_agency_logo?->path;

            $data['agency']['logo_id'] = $this->attachment_id ? $sub_agency_logo?->uuid : null;
            $data['agency']['fallback_image'] = get_profile_picture($user); //so that frontend don't need to send request again after deleting the image

            // if($this->attachment_id == null) {
            //     $data['agency']['logo'] = get_profile_picture($user);
            // } else {
            //     $sub_agency_logo = get_sub_agency_logo($this, $user);
            //     $data['agency']['logo'] = getAttachmentBasePath() . $sub_agency_logo?->path;
            //     $data['agency']['logo_id'] = $sub_agency_logo?->uuid;
            // }

            $data['agency']['slug'] = $agency->slug;
            $data['agency']['id'] = $user->uuid;
        }

        return $data;
    }

    public function get_location()
    {
        $location = [
            'state_id' => null,
            'state' => null,
            'city_id' => null,
            'city' => null,
        ];

        if ($this->state) {
            $location['state_id'] = $this->state->uuid;
            $location['state'] = $this->state->name;
        }

        if ($this->city) {
            $location['city_id'] = $this->city->uuid;
            $location['city'] = $this->city->name;
        }

        return $location;
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
        $seo_title_format = $this->seo_title ? $this->seo_title : settings('job_title');

        return replacePlaceholders($seo_title_format, [
            '%job_title%' => $this->title,
            '%job_location%' => sprintf('%s, %s', $this->city?->name, $this->state?->name),
            '%job_employment_type%' => $this->employment_type,
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }

    private function generateSeoDescription($site_name, $separator)
    {
        $seo_description_format = $this->seo_description ? $this->seo_description : settings('job_description');

        return replacePlaceholders($seo_description_format, [
            '%job_description%' => $this->description,
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }

    private function get_user_profile_url($user, $agency)
    {
        $role = $user->role;

        switch ($role) {
            case 'recruiter':
                return  $agency->slug . "/recruiter";
            case 'advidsor':
                return  $agency->slug . "/advisor";
            default:
                return $agency->slug;
        }
    }
}