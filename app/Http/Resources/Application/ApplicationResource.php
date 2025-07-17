<?php

namespace App\Http\Resources\Application;

use App\Models\Job;
use App\Models\Review;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        $logged_in_user = get_auth_user();
        $user = $this->user;
        // $job = $this->job;
        $job = Job::where('id', '=', $this->job_id)->first();

        if (!$user) {
            return []; // not needed now, but kept it for safety, just in case user was deleted (softly or hardly)
        }

        $creative_reviews_avg = Review::where('target_id', $user->id)->avg('rating') ?? 0.0;

        return [
            'type' => 'applications',
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'creative_id' => $user->creative ? $user->creative->uuid : '',
            'advisor_id' => $job->advisor_id ?? null,
            'user' => $user->first_name . ' ' . $user->last_name,
            'user_first_name' => $user->first_name,
            'user_last_name' => $user->last_name,
            'slug' => get_user_slug($user),
            'creative_category' => $user->creative?->category?->name,
            'creative_title' => $user->creative->title,
            'profile_image' => $this->get_profile_image($user),
            'user_thumbnail' => get_user_thumbnail($user),
            'user_profile_id' => $user->id,
            'job_id' => $job->uuid,
            'job_title' => $job->title,
            'job_apply_type' => $job->apply_type,
            'resume_url' => $this->get_resume_url($user, $logged_in_user), //isset($this->attachment) ? asset('storage/'.$this->attachment->path) : null,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
            'removed_from_recent' => $this?->removed_from_recent ? $this->removed_from_recent : false,
            'creative_reviews_avg' => sprintf('%0.2f', $creative_reviews_avg),
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

    public function get_profile_image($user)
    {
        return isset($user->profile_picture) ? (getAttachmentBasePath() . $user->profile_picture->path) : '';
        // return isset( $user->profile_picture ) ? getAttachmentBasePath() . $user->profile_picture->path : asset( 'assets/img/placeholder.png' );
    }

    private function get_resume_url($user, $logged_in_user)
    {
        if (isset($user->resume)) {
            return getAttachmentBasePath() . $user->resume->path;
        } else {
            $resume_filename = sprintf('%s_%s_AdAgencyCreatives_%s', $user->first_name, $user->last_name, date('Y-m-d'));

            $u2 = $logged_in_user?->uuid ? $logged_in_user->uuid : request()->input('filter.user_id');
            return route('download.resume', ['name' => $resume_filename, 'u1' => $user->uuid, 'u2' => $u2]);
        }
    }
}
