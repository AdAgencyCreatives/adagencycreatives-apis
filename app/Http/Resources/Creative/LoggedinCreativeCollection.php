<?php

namespace App\Http\Resources\Creative;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LoggedinCreativeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $arr = parent::toArray($request);

        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i] = new CreativeResource($arr[$i]);
        }

        return $arr;
    }
}