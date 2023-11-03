<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'body' => $this->body,
            'read_at' => $this->read_at ? $this->read_at->format(config('global.datetime_format')) : null,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }
}
