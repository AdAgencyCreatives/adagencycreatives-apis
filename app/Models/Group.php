<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
    ];

    public const STATUSES = [
        'PUBLIC' => 0,
        'PRIVATE' => 1,
        'HIDDEN' => 2,
    ];

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'resource_id');
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
        return $this->invitations()->where('inviter_user_id', $user->id)->where('status', GroupInvitation::STATUSES['PENDING'])->exists();
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
        static::created(function () {
            Cache::forget('all_groups');
        });

        static::updated(function () {
            Cache::forget('all_groups');
        });

        static::deleted(function () {
            Cache::forget('all_groups');
        });
    }
}
