<?php

namespace App\Http\Resources\Agency;

use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'agencies',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'name' => $this->name,
            'logo' => $this->attachment ? getAttachmentBasePath().$this->attachment->path : null,
            'about' => $this->about,
            'size' => $this->size,
            'type_of_work' => $this->type_of_work,
            'industry_specialty' => getIndustryNames($this->industry_specialty),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
