<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'resource_id',
        'resource_type',
        'path',
        'extension',
        'created_at',
        'updated_at',
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
        if ($user) {
            return $query->where('user_id', $user->id);
        }
    }

    public function scopePostId(Builder $query, $post_id)
    {
        $post = Post::where('uuid', $post_id)->first();
        if ($post) {
            return $query->where('post_id', $post->id);
        }
    }

    public function scopeResourceType(Builder $query, $resource_type)
    {
        return $query->where('resource_type', $resource_type);
    }
}
