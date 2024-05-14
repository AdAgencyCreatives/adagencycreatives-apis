<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        if(!$user)
        return [];

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $user->uuid,
            'user' => $user->first_name.' '.$user->last_name,
            'user_slug' => get_user_slug($user),
            'profile_picture' => get_profile_picture($user),
            'user_thumbnail' => get_user_thumbnail($user),
            'post_id' => $this->post->uuid,
            'parent_id' => isset($this->parent) ? $this->parent->uuid : null,
            'content' => $this->content,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'human_readable_date' => $this->created_at->diffForHumans(),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}