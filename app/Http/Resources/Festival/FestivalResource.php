<?php

namespace App\Http\Resources\Festival;

use Illuminate\Http\Resources\Json\JsonResource;

class FestivalResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'email' => $this->email ?? '',
            'title' => $this->title ?? '',
            'path' => $this->getFullPath(),
            'category' => $this->category ?? ''
        ];
    }

    public function getFullPath()
    {
        if($this->path){
            return getAttachmentBasePath() . $this->path;
        }
        return asset('assets/img/placeholder.png');;
    }
}