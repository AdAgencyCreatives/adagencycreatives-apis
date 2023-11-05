<?php

namespace App\Http\Resources\Notification;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'user' => new UserResource($this->user),
            'type' => $this->type,
            'body' => $this->body,
            'message' => $this->message,
            'read_at' => $this->read_at ? $this->read_at->format(config('global.datetime_format')) : null,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }
}
