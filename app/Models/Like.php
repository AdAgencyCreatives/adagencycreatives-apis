<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'post_id',
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
         $user = User::where('uuid', $user_id)->firstOrFail();

         return $query->where('user_id', $user->id);
     }

    public function scopePostId(Builder $query, $post_id)
    {
        $post = Post::where('uuid', $post_id)->firstOrFail();

        return $query->where('post_id', $post->id);
    }
}
