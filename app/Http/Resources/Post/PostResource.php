<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Attachment\AttachmentCollection;
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
            'content' => $this->content,
            'status' => $this->status,
            'attachments' => new AttachmentCollection($this->attachments),
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
