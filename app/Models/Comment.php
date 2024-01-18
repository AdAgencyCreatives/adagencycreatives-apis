<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
    use App\Traits\ActivityLoggerTrait;


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
        static::created(function () {
            Cache::forget('trending_posts');
        });

        static::updated(function () {
            Cache::forget('trending_posts');
        });

        static::deleted(function () {
            Cache::forget('trending_posts');
        });
    }
}