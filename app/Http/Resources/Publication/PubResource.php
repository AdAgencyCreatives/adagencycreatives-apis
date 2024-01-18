<?php

namespace App\Http\Resources\Publication;

use Illuminate\Http\Resources\Json\JsonResource;

class PubResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'link' => $this->link,
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