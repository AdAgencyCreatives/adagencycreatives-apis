<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'group_id',
        'content',
        'status',
    ];

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'resource_id');
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
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function scopeGroupId(Builder $query, $group_id)
    {
        $group = Group::where('uuid', $group_id)->firstOrFail();

        return $query->where('group_id', $group->id);
    }
}