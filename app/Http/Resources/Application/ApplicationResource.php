<?php

namespace App\Http\Resources\Application;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        $logged_in_user = request()->user();
        $user = $this->user;
        $job = $this->job;

        return [
            'type' => 'applications',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'creative_id' => $user->creative ? $user->creative->uuid : '',
            'advisor_id' => $job->advisor_id ?? null,
            'user' => $user->first_name . ' ' . $user->last_name,
            'slug' => get_user_slug($user),
            'user_profile_id' => $user->id,
            'job_id' => $job->uuid,
            'job_title' => $job->title,
            'resume_url' => $this->get_resume_url($user, $logged_in_user), //isset($this->attachment) ? asset('storage/'.$this->attachment->path) : null,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),

            'relationships' => [
                'notes' => [
                    'links' => [
                        'related' => route('notes.index') . '?filter[application_id]=' . $this->uuid,
                    ],
                ],
                // 'job' => new JobResource($this->job),
            ],

        ];
    }

    private function get_resume_url($user, $logged_in_user)
    {
        if (isset($user->resume)) {
            return getAttachmentBasePath() . $user->resume->path;
        } else {
            $resume_filename = sprintf('%s_%s_AdAgencyCreatives_%s', $user->first_name, $user->last_name, date('Y-m-d'));

            return route('download.resume', ['name' => $resume_filename, 'u1' => $user->uuid, 'u2' => $logged_in_user?->uuid]);
        }
    }
}
