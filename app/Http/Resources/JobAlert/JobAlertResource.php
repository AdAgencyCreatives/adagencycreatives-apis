<?php

namespace App\Http\Resources\JobAlert;

use Illuminate\Http\Resources\Json\JsonResource;

class JobAlertResource extends JsonResource
{
    public function toArray($request)
    {
        $category = $this->category;

        return [
            'uuid' => $this->uuid,
            'user_id' => $this->user->uuid,
            'category' => $category->name,
            'category_id' => $category->uuid,
            'status' => $this->status == 1 ? 'Active' : 'Inactive',
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}