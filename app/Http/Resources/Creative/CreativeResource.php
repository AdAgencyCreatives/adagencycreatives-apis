<?php

namespace App\Http\Resources\Creative;

use App\Http\Resources\Link\LinkCollection;
use App\Http\Resources\Review\ReviewResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CreativeResource extends JsonResource
{
    private $creative_category;

    private $location;

    public function toArray($request)
    {
        $allowBase64 = $request->has('base64') && $request?->base64 == 'yes';
        $user = $this->user;
        $this->creative_category = isset($this->category) ? $this->category->name : null;

        $this->location = $this->get_location($user);

        return [
            'type' => 'creatives',
            'test_id' => $this->id,
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $this->get_email($user),
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->creative_category,
            'profile_image' => $this->get_profile_image($user),
            'profile_image_base64' =>  $allowBase64 ? $this->get_profile_image_base64($user) : '',
            'user_thumbnail' => $this->get_user_thumbnail($user),
            'user_thumbnail_base64' => $allowBase64 ? $this->get_user_thumbnail_base64($user) : '',
            'years_of_experience' => $this->years_of_experience,
            'portfolio_items' => $this->get_portfolio_items($user),
            'portfolio_items_base64' => $allowBase64 ? $this->get_portfolio_items_base64($user) : '',
            'about' => $this->about,
            'employment_type' => getEmploymentTypes($this->employment_type),
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
            'phone_number' => $this->get_phone_number($user),
            'location' => $this->location,
            'resume' => get_resume($user),
            'portfolio_website' => $this->get_website_preview($user),
            'portfolio_website_base64' => $allowBase64 ? $this->get_website_preview_base64($user) : '',
            'links' => new LinkCollection($user->links),
            'seo' => $this->generate_seo(),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
            'featured_at' => $this->featured_at ? $this->featured_at?->format(config('global.datetime_format')) : null,
            'is_welcomed' => $this->is_welcomed,
            'welcomed_at' => $this->welcomed_at ? $this->welcomed_at?->format(config('global.datetime_format')) : null,
            'welcome_queued_at' => $this->welcome_queued_at ? $this->welcome_queued_at?->format(config('global.datetime_format')) : null,
            'profile_complete_progress' => $user?->profile_complete_progress ?? '0',
            'profile_completed_at' => $user?->profile_completed_at ? $this->profile_completed_at?->format(config('global.datetime_format')) : null,
            'profile_completion_reminded_at' => $user?->profile_completion_reminded_at ? $this->profile_completion_reminded_at?->format(config('global.datetime_format')) : null,
            'reviews' => $this->get_reviews($user),
        ];
    }

    public function get_email($user)
    {
        return $user->email;
    }

    public function get_phone_number($user)
    {
        return $user->personal_phone ? $user->personal_phone->phone_number : null;
    }

    public function get_profile_image($user)
    {
        return isset($user->profile_picture) ? (getAttachmentBasePath() . $user->profile_picture->path) : '';
        // return isset( $user->profile_picture ) ? getAttachmentBasePath() . $user->profile_picture->path : asset( 'assets/img/placeholder.png' );
    }

    public function get_profile_image_base64($user, $thumbWidth = 200)
    {
        return get_thumb_base64($user?->profile_picture ?? null, $thumbWidth);
    }

    public function get_user_thumbnail($user)
    {
        return isset($user->user_thumbnail) ? getAttachmentBasePath() . $user->user_thumbnail->path : '';
    }

    public function get_user_thumbnail_base64($user, $thumbWidth = 200)
    {
        return get_thumb_base64($user?->user_thumbnail ?? null, $thumbWidth);
    }

    public function get_website_preview($user)
    {
        return $user->portfolio_website_preview ? getAttachmentBasePath() . $user->portfolio_website_preview->path : '';
    }

    public function get_website_preview_base64($user, $thumbWidth = 400)
    {
        return get_thumb_base64($user?->portfolio_website_preview ?? null, $thumbWidth);
    }

    public function get_portfolio_items($user)
    {
        $portfolio_items = [];

        foreach ($user->portfolio_items as $item) {
            $portfolio_items[] = getAttachmentBasePath() . $item->path;
        }
        return $portfolio_items;
    }

    public function get_portfolio_items_base64($user, $thumbWidth = 200)
    {
        $portfolio_items_base64 = [];

        foreach ($user->portfolio_items as $item) {
            try {
                $portfolio_items_base64[] = get_thumb_base64($item, $thumbWidth);
                // $portfolio_items_base64[] = 'data:image/' . $item->extension . ';charset=utf-8;base64,' .  base64_encode(file_get_contents(getAttachmentBasePath() . $item->path));
            } catch (\Exception $e) {
            }
        }
        return $portfolio_items_base64;
    }

    public function get_location($user)
    {
        $address = $user->addresses ? collect($user->addresses)->firstWhere('label', 'personal') : null;

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
        $seo_title_format = $this->seo_title ? $this->seo_title : settings('creative_title');

        return replacePlaceholders($seo_title_format, [
            '%creatives_first_name%' => $this->user->first_name,
            '%creatives_last_name%' => $this->user->last_name,
            '%creatives_title%' => $this->title,
            '%creatives_location%' => isset($this->location) ? sprintf('%s, %s', ($this->location['city'] ? $this->location['city'] : ''), ($this->location['state'] ? $this->location['state'] : '')) : '',
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }

    private function generateSeoDescription($site_name, $separator)
    {
        $seo_description_format = $this->seo_description ? $this->seo_description : settings('creative_description');

        return replacePlaceholders($seo_description_format, [
            '%creatives_about%' => strip_tags($this->about),
            '%site_name%' => $site_name,
            '%separator%' => $separator,
        ]);
    }

    public function get_resume($user, $logged_in_user, $subscription_status, $is_friend)
    {
        // dd( $subscription_status );
        //User is viewing own profile
        if ($logged_in_user->id == $user->id) {
            return $this->get_resume_url($user);
        }

        if ($logged_in_user->role === 'agency' && $subscription_status !== 'active') {
            return null;
        }

        if ($logged_in_user->role === 'creative' && !$is_friend) {
            return null;
        }

        return $this->get_resume_url($user, $logged_in_user);
    }

    private function get_resume_url($user, $logged_in_user)
    {
        if (isset($user->resume)) {
            return getAttachmentBasePath() . $user->resume->path;
        } else {
            $resume_filename = sprintf('%s_%s_AdAgencyCreatives_%s', $user->first_name, $user->last_name, date('Y-m-d'));

            return route('download.resume', ['name' => $resume_filename, 'uuid1' => $user->uuid, 'uuid2' => $logged_in_user->uuid]);
        }
    }

    public function get_reviews($user)
    {
        $reviews = [];

        foreach ($user->receivedReviews as $item) {
            $reviews[] = new ReviewResource($item);
        }
        return $reviews;
    }
}
