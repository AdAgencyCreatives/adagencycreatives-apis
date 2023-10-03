<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        $url = $this->isPlaceholderUrl($this->path) ? $this->path : getAttachmentBasePath().$this->path;

        return [
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'resource_type' => $this->resource_type,
            'url' => $url,
            'name' => $this->name,
            'extension' => $this->extension,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }

    private function isPlaceholderUrl($url)
    {
        return strpos($url, 'https://via.placeholder.com/') === 0;
    }
}
