<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'group_id',
        'content',
        'status',
        'updated_at',
        'edited_at',
        'pinned_at',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'pinned_at' => 'datetime',
    ];

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'resource_id')
            ->whereIn('resource_type', [

                'post_attachment_image',
                'post_attachment_video',

            ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    public function firstThreeComments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->take(3)->latest();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    public const STATUSES = [
        'DRAFT' => 0,
        'PUBLISHED' => 1,
        'ARCHIVED' => 2,
    ];

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Post::STATUSES['DRAFT']:
                return 'draft';
            case Post::STATUSES['PUBLISHED']:
                return 'published';
            case Post::STATUSES['ARCHIVED']:
                return 'archived';
            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'draft':
                $this->attributes['status'] = Post::STATUSES['DRAFT'];
                break;
            case 'archived':
                $this->attributes['status'] = Post::STATUSES['ARCHIVED'];
                break;
            default:
                $this->attributes['status'] = Post::STATUSES['PUBLISHED'];
                break;
        }
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        if ($user) {
            return $query->where('user_id', $user->id);
        } else {
            return $query->where('user_id', 0);
        }
    }

    public function scopeGroupId(Builder $query, $group_id)
    {
        $group = Group::where('uuid', $group_id)->first();
        if ($group) {
            return $query->where('group_id', $group->id);
        } else {
            return $query->where('group_id', 0);
        }
    }

    protected static function booted()
    {
        static::created(function ($post) {
            Cache::forget('trending_posts');

            $pattern = '/creative\/([-\w]+)/';

            // Match user slugs in the post content
            preg_match_all($pattern, $post->content, $matches);

            // Extract unique user slugs
            $user_slugs = array_unique($matches[1]);
            $author = $post->user;
            $group = Group::find($post->group_id); //it gives us group id as integer because this function triggers after post is created and it gives us newly created post object

            foreach ($user_slugs as $slug) {
                $user = User::where('username', $slug)->first(); //Person who is mentioned in the post

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';
                $author_url = env('FRONTEND_URL') . '/creative/' . $author->username;
                $message = "<a href='{$author_url}'>{$author->full_name}</a> commented on you in a <a href='{$group_url}#{$post->uuid}'>post</a>";
                $data = [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'body' => $post->id,
                    'type' => 'lounge_mention',
                    'message' => $message
                ];

                Notification::create($data);
            }
        });

        static::updated(function ($post) {
            Cache::forget('trending_posts');

            $pattern = '/creative\/([-\w]+)/';

            // Match user slugs in the post content
            preg_match_all($pattern, $post->content, $matches);

            // Extract unique user slugs
            $user_slugs = array_unique($matches[1]);
            $author = $post->user;
            $group = Group::find($post->group_id); //it gives us group id as integer because this function triggers after post is created and it gives us newly created post object

            foreach ($user_slugs as $slug) {
                $user = User::where('username', $slug)->first(); //Person who is mentioned in the post

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';
                $author_url = env('FRONTEND_URL') . '/creative/' . $author->username;
                $message = "<a href='{$author_url}'>{$author->full_name}</a> commented on you in a <a href='{$group_url}#{$post->uuid}'>post</a>";
                $data = [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'body' => $post->id,
                    'type' => 'lounge_mention',
                    'message' => $message
                ];

                $notification = Notification::where([
                    'user_id' => $user->id,
                    'body' => $post->id,
                    'type' => 'lounge_mention'
                ])->orderByDesc('created_at')->first();

                if ($notification) {
                    $notification->update([
                        'read_at' => null,
                        'created_at' => now()
                    ]);
                } else {
                    Notification::create($data);
                }
            }
        });

        static::deleted(function ($post) {
            Cache::forget('trending_posts');

            PostReaction::where('post_id', $post->id)->delete();
            Comment::where('post_id', $post->id)->delete();
        });
    }
}