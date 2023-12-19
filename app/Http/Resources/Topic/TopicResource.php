<?php

namespace App\Http\Resources\Topic;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            // 'created_at' => $this->created_at->format(config('global.datetime_format')),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }
}