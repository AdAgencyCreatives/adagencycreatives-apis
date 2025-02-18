<?php

namespace App\Http\Resources\Featured_Cities;

use App\Models\Job;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationWithJobsCountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'count' => $this->job_count,
            'preview_link' => $this->get_preview_link(),
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
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
