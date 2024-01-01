<?php

namespace App\Http\Resources\Featured_Cities;

use App\Models\Job;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request)
    {
        $location = $this->location;
        $jobs_count = Job::where('city_id', $location->id)->where('status', 1)->count(); //Approved jobs only

        return [
            'id' => $this->id,
            'uuid' => $location->uuid,
            'name' => $location->name,
            'slug' => $location->slug,
            'count' => $jobs_count,
            'preview_link' => $this->get_preview_link(),
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
