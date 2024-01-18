<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Comment\CommentCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class TrendingPostResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->uuid,
            'user_id' => $user->uuid ?? '',
            'author' => $user->full_name ?? '',
            'author_slug' => get_user_slug($user),
            'author_avatar' => get_profile_picture($user ?? null),
            'content' => $this->content,
            'status' => $this->status,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'human_readable_date' => $this->created_at->diffForHumans(),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
            'relationships' => [
                'comments' => [
                    'links' => [
                        'related' => route('comments.index').'?filter[post_id]='.$this->uuid,
                    ],
                ],
                'likes' => [
                    'links' => [
                        'related' => route('likes.index').'?filter[post_id]='.$this->uuid,
                    ],
                ],
            ],
        ];
    }
}