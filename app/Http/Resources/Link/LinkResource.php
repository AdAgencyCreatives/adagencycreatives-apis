<?php

namespace App\Http\Resources\Link;

use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'links',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'label' => $this->label,
            'url' => $this->url,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
