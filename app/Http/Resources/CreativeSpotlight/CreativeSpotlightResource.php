<?php

namespace App\Http\Resources\CreativeSpotlight;

use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CreativeSpotlightResource extends JsonResource
{
    public function toArray($request)
    {
        $carbonDate = Carbon::parse($this->created_at);

        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => getAttachmentBasePath() . $this->path,
            'status' => $this->status,
            'created_at' => $carbonDate->format('M d, Y'),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
    ];

    }

}