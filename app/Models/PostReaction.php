<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class PostReaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'post_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        if($user) return $query->where('user_id', $user->id);

        return $query->where('user_id', 0);
    }

    public function scopePostId(Builder $query, $group_id)
    {
        $post = Post::where('uuid', $group_id)->first();
        if($post) return $query->where('post_id', $post->id);

        return $query->where('post_id', 0);
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