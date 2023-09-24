<?php

namespace App\Http\Resources\JobAlert;

use Illuminate\Http\Resources\Json\JsonResource;

class JobAlertResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'user_id' => $this->user->uuid,
            'category' => $this->category->name,
            'status' => $this->status == 1 ? 'Active' : 'Inactive',
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
