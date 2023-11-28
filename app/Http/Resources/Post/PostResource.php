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
            'user_id' => $user->uuid ?? '',
            'group_id' => $this->group?->uuid,
            'author' => $user->full_name ?? '',
            'author_slug' => get_user_slug($user),
            'author_avatar' => get_profile_picture($user ?? null),
            'content' => $this->content,
            'status' => $this->status,
            'attachments' => new AttachmentCollection($this->attachments),
            'comments_count' => $this->comments_count,
            'comments' => new CommentCollection($this->comments),
            'likes_count' => $this->likes_count,
            'reactions' => $this->get_reactions_count(),
            'has_liked_post' => $this->user_has_liked,
            // 'created_at' => $this->created_at->format(config('global.datetime_format')),
            'created_at' => $this->created_at->diffForHumans(),
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

    public function get_reactions_count()
    {
        $allReactionTypes = ['like', 'heart', 'laugh'];
        $reactionsGrouped = $this->reactions->groupBy('type');
        $reactionsCount = collect($allReactionTypes)->mapWithKeys(function ($type) use ($reactionsGrouped) {
            return [$type => $reactionsGrouped->get($type, collect())->count()];
        });

        $authenticatedUserId = auth()->id();

        // Include information about whether the user has liked, laughed, or reacted in any other way
        $userReactions = collect($allReactionTypes)->mapWithKeys(function ($type) use ($reactionsGrouped, $authenticatedUserId) {
            return ['user_has_'.$type => $reactionsGrouped->get($type, collect())->where('user_id', $authenticatedUserId)->isNotEmpty()];
        });

        return $reactionsCount->merge($userReactions);

        return $reactionsCount;
    }

    public function get_image($user)
    {
        if(!$user) return '';

        if ($user->role == 'creative' || $user->role == 'admin') {
            return isset($user->profile_picture) ? getAttachmentBasePath().$user->profile_picture->path : null;
        } elseif ($user->role == 'agency' || $user->role == 'advisor') {
            return isset($user->agency_logo) ? getAttachmentBasePath().$user->agency_logo->path : null;
        }

    }
}