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
            'city' => $this->city->name,
            'state' => $this->state->name,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

        ];
    }
}
