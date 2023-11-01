<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Attachment\AttachmentCollection;
use App\Http\Resources\Comment\CommentCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->uuid,
            'user_id' => $user->uuid,
            'group_id' => $this->group->uuid,
            'author' => $user->first_name.' '.$user->last_name,
            'author_avatar' => $this->get_image($user),
            'content' => $this->content,
            'status' => $this->status,
            // 'attachments' => new AttachmentCollection($this->attachments),
            'comments_count' => $this->comments_count,
            'comments' => new CommentCollection($this->comments),
            'likes_count' => $this->likes_count,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
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

    public function get_image($user)
    {
        if ($user->role == 'creative' || $user->role == 'admin') {
            return isset($user->profile_picture) ? getAttachmentBasePath().$user->profile_picture->path : null;
        } elseif ($user->role == 'agency' || $user->role == 'advisor') {
            return isset($user->agency_logo) ? getAttachmentBasePath().$user->agency_logo->path : null;
        }

    }
}
