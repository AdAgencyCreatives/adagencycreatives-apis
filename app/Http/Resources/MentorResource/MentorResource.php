<?php

namespace App\Http\Resources\MentorResource;

use Illuminate\Http\Resources\Json\JsonResource;

class MentorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'description' => $this->description,
            'topic' => $this->topic?->title,
            'topic_slug' => $this->topic?->slug,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }
}