<?php

namespace App\Http\Resources\Phone;

use Illuminate\Http\Resources\Json\JsonResource;

class PhoneResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'phones-numbers',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'label' => $this->label,
            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at->format(config('ad-agency-creatives.datetime_format')),
            'updated_at' => $this->created_at->format(config('ad-agency-creatives.datetime_format')),
        ];
    }
}
