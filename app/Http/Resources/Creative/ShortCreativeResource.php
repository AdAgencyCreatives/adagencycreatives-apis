<?php

namespace App\Http\Resources\Creative;

use App\Http\Resources\Link\LinkCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortCreativeResource extends JsonResource
{
    private $creative_category;

    private $location;

    public function toArray($request)
    {
        $user = $this->user;
        $this->creative_category = isset($this->category) ? $this->category->name : null;

        $this->location = $this->get_location($user);

        return [
            'type' => 'creatives',

            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'name' => $user->first_name . ' ' . $user->last_name,
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->creative_category,
            'profile_image' => $this->get_profile_image($user),
            'user_thumbnail' => $this->get_user_thumbnail($user),
            'location' => $this->location,
        ];
    }

    public function get_profile_image($user)
    {
        return isset($user->profile_picture) ? getAttachmentBasePath() . $user->profile_picture->path : asset('assets/img/placeholder.png');
    }

    public function get_user_thumbnail($user)
    {
        return isset($user->user_thumbnail) ? getAttachmentBasePath() . $user->user_thumbnail->path : "";
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
}
