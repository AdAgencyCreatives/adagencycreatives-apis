<?php

namespace App\Http\Resources\Faq;

use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray($request)
    {

        $data = [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
        ];

        return $data;
    }
}
