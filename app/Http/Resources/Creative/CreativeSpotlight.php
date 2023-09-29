<?php

namespace App\Http\Resources\Creative;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CreativeSpotlight extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        $title = sprintf('%s, %s', $user->creative->category->name, $user->first_name.' '.$user->last_name);

        return [
            'id' => $this->uuid,
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'url' => getAttachmentBasePath().$this->path,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
