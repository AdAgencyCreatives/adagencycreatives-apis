<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'post_id',
        'parent_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function scopePostId(Builder $query, $post_id)
    {
        $post = Post::where('uuid', $post_id)->firstOrFail();

        return $query->where('post_id', $post->id);
    }

    protected static function booted()
    {
        static::created(function ($comment) {
            Cache::forget('trending_posts');

            $pattern = '/creative\/([-\w]+)/';

            // Match user slugs in the post content
            preg_match_all($pattern, $comment->content, $matches);

            // Extract unique user slugs
            $user_slugs = array_unique($matches[1]);
            $author = $comment->user;
            $group = Group::find($comment->post->group_id); //it gives us group id as integer because this function triggers after post is created and it gives us newly created post object

            foreach ($user_slugs as $slug) {
                $user = User::where('username', $slug)->first(); //Person who is mentioned in the post

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';
                $message = "<a href='{env('FRONTEND_URL')}/creative/{$author->username}'>{$author->full_name}</a> commented on you in a <a href='{$group_url}#{$comment->post->uuid}'>post</a>";
                $data = [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'body' => $comment->post->id,
                    'type' => 'lounge_mention',
                    'message' => $message
                ];

                Notification::create($data);
            }
        });

        static::updated(function ($comment) {
            Cache::forget('trending_posts');

            $pattern = '/creative\/([-\w]+)/';

            // Match user slugs in the post content
            preg_match_all($pattern, $comment->content, $matches);

            // Extract unique user slugs
            $user_slugs = array_unique($matches[1]);
            $author = $comment->user;
            $group = Group::find($comment->post->group_id); //it gives us group id as integer because this function triggers after post is created and it gives us newly created post object

            foreach ($user_slugs as $slug) {
                $user = User::where('username', $slug)->first(); //Person who is mentioned in the post

                $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';
                $message = "<a href='{env('FRONTEND_URL')}/creative/{$author->username}'>{$author->full_name}</a> commented on you in a <a href='{$group_url}#{$comment->post->uuid}'>post</a>";
                $data = [
                    'uuid' => Str::uuid(),
                    'user_id' => $user->id,
                    'body' => $comment->post->id,
                    'type' => 'lounge_mention',
                    'message' => $message
                ];

                $notification = Notification::where([
                    'user_id' => $user->id,
                    'body' => $comment->post->id,
                    'type' => 'lounge_mention'
                ])->orderByDesc('updated_at')->first();

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

        static::deleted(function () {
            Cache::forget('trending_posts');
        });
    }
}