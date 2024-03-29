<?php

namespace App\Http\Resources\CreativeSpotlight;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CreativeSpotlightResource extends JsonResource
{
    public function toArray($request)
    {
        $created_at = Carbon::parse($this->created_at);
        $updated_at = Carbon::parse($this?->updated_at ? $this->updated_at : $this->created_at);

        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => getAttachmentBasePath() . $this->path,
            'status' => $this->status,
            'created_at' => $created_at->format('M d, Y'),
            'updated_at' => $updated_at->format('M d, Y'),
        ];
    }
}
