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
            'preview_link' => $this->get_preview_link(),
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }

    function get_preview_link()
    {
        $defaultImage = asset('assets/img/placeholder.png');
        $attachmentBasePath = getAttachmentBasePath();
        if($this->preview_link){
            return $attachmentBasePath . $this->preview_link;
        }

        return $defaultImage;
    }
}