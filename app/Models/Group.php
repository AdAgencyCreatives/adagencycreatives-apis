<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggerTrait;

class Group extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    public const STATUSES = [
        'PUBLIC' => 0,
        'PRIVATE' => 1,
        'HIDDEN' => 2,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'resource_id')->where('resource_type', 'cover_image');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }

    public function isMember(User $user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function isInvitationAlreadySent(User $user)
    {
        return $this->invitations()->where('invitee_user_id', $user->id)->where('status', GroupInvitation::STATUSES['PENDING'])->exists();
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->firstOrFail();
        return $query->where('user_id', $user->id);
    }

    public function scopeMemberId(Builder $query, $user_id)
    {
        $user_id = User::where('uuid', $user_id)->pluck('id');
        $group_id = GroupMember::where('user_id', $user_id)->where('role', '!=', 1)->pluck('group_id');

        return $query->whereIn('id', $group_id);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Group::STATUSES['PUBLIC']:
                return 'public';
            case Group::STATUSES['PRIVATE']:
                return 'private';
            case Group::STATUSES['HIDDEN']:
                return 'hidden';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'public':
                $this->attributes['status'] = Group::STATUSES['PUBLIC'];
                break;
            case 'private':
                $this->attributes['status'] = Group::STATUSES['PRIVATE'];
                break;
            default:
                $this->attributes['status'] = Group::STATUSES['HIDDEN'];
                break;
        }
    }

    protected static function booted()
    {
        static::created(function ($group) {
            Cache::forget('all_groups');
            if ($group->slug == null) {
                $group->slug = Str::slug($group->name);
                $group->save();
            }
        });

        static::updated(function () {
            Cache::forget('all_groups');
        });

        static::deleted(function ($group) {
            Cache::forget('all_groups');
            Post::where('group_id', $group->id)->delete();
        });
    }
}