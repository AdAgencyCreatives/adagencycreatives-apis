<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->updated_at->format(config('global.datetime_format')),
        ];
    }
}
