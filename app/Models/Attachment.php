<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'resource_id',
        'resource_type',
        'path',
        'name',
        'extension',
        'status',
        'created_at',
        'updated_at',
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'ACTIVE' => 1,
        'INACTIVE' => 2,
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
        } else {
            return $query->where('user_id', 0);
        }
    }

    public function scopePostId(Builder $query, $post_id)
    {
        $post = Post::where('uuid', $post_id)->first();
        if ($post) {
            return $query->where('resource_id', $post->id);
        } else {
            return $query->where('resource_id', 0);
        }

    }

    public function scopeResourceType(Builder $query, $resource_type)
    {
        return $query->where('resource_type', $resource_type);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Attachment::STATUSES['PENDING']:
                return 'pending';
            case Attachment::STATUSES['ACTIVE']:
                return 'active';
            case Attachment::STATUSES['INACTIVE']:
                return 'inactive';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'active':
                $this->attributes['status'] = Attachment::STATUSES['ACTIVE'];
                break;
            case 'inactive':
                $this->attributes['status'] = Attachment::STATUSES['INACTIVE'];
                break;
            default:
                $this->attributes['status'] = Attachment::STATUSES['PENDING'];
                break;
        }
    }

    protected static function booted()
    {
        if (! App::runningInConsole()) {
            static::created(function () {
                Cache::forget('all_users_with_attachments');
            });

            static::updated(function () {
                Cache::forget('all_users_with_attachments');
            });

            static::deleted(function () {
                Cache::forget('all_users_with_attachments'); //cache for displaying count of attachments on admin dashboard for Media page
            });
        }

    }
}