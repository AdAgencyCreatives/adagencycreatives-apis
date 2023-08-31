<?php

namespace App\Http\Resources\Application;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'type' => 'applications',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'user' => $user->first_name.' '.$user->last_name,
            'user_profile_id' => $user->id,
            'job_id' => $this->job->uuid,
            'resume_url' => asset('storage/'.$this->attachment->path),
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

            'relationships' => [
                'notes' => [
                    'links' => [
                        'related' => route('notes.index').'?filter[application_id]='.$this->uuid,
                    ],
                ],
            ],

        ];
    }
}
