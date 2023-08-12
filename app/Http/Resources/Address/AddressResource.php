<?php

namespace App\Http\Resources\Address;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'addresses',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'label' => $this->label,
            'street_1' => $this->street_1,
            'street_2' => $this->street_2,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

        ];
    }
}
