<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        $url = $this->isPlaceholderUrl($this->path) ? $this->path : getAttachmentBasePath().$this->path;
        $user = $this->user;

        return [

            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'user_name' => $user->first_name.' '.$user->last_name,
            'resource_type' => $this->resource_type,
            'url' => $url,
            'name' => $this->name,
            'extension' => $this->extension,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }

    private function isPlaceholderUrl($url)
    {
        return strpos($url, 'https://via.placeholder.com/') === 0;
    }
}
