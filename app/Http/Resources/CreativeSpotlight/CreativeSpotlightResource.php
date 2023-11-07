<?php

namespace App\Http\Resources\CreativeSpotlight;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CreativeSpotlightResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        $data = [
        'id' => $this->uuid,
        'title' => $this->title,
        'slug' => $this->slug,
        'url' => getAttachmentBasePath() . $this->path,
        'status' => $this->status,
        'created_at' => $this->created_at->format(config('global.datetime_format')),
        'updated_at' => $this->updated_at->format(config('global.datetime_format')),
    ];

        // Check if a user is authenticated
        if ($request->user()) {
            // If authenticated, further check if the user is an admin
            if ($request->user()->role === 'admin') {
                $data['user'] = new UserResource($user);
            }
        }

        return $data;
    }

}
