<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class CompactUserResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'type' => 'users',
            'uuid' => $this->uuid,
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'is_visible' => $this->is_visible,
            'image' => get_profile_picture($this),
            'user_thumbnail' => get_user_thumbnail($this),
            'image_id' => get_profile_picture_id($this),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
            'deleted_at' => $this?->deleted_at ? $this->deleted_at->format(config('global.datetime_format')) : '',
            'email_notifications_enabled' => $this->email_notifications_enabled,
        ];

        return $data;
    }
}