<?php

namespace App\Http\Resources\Note;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'type' => 'notes',
            'id' => $this->uuid,
            'user_id' => $this->user->uuid,
            'application_id' => $this->application->uuid,
            'body' => $this->body,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

        ];

    }
}
