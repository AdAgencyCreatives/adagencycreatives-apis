<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'inviter_user_id',
        'invitee_user_id',
        'group_id',
        'status',
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'ACCEPTED' => 1,
        'REJECTED' => 2,
    ];

    public function inviter()
    {
        return $this->hasOne(User::class, 'id', 'inviter_user_id');
    }

    public function invitee()
    {
        return $this->hasOne(User::class, 'id', 'invitee_user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case GroupInvitation::STATUSES['PENDING']:
                return 'pending';
            case GroupInvitation::STATUSES['ACCEPTED']:
                return 'accepted';
            case GroupInvitation::STATUSES['REJECTED']:
                return 'rejected';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'pending':
                $this->attributes['status'] = GroupInvitation::STATUSES['PENDING'];
                break;
            case 'accepted':
                $this->attributes['status'] = GroupInvitation::STATUSES['ACCEPTED'];
                break;
            default:
                $this->attributes['status'] = GroupInvitation::STATUSES['REJECTED'];
                break;
        }
    }

    public function scopeReceiverId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        return $query->where('invitee_user_id', $user->id);
    }

    public function scopeSenderId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        return $query->where('inviter_user_id', $user->id);
    }

    public function scopeGroupId(Builder $query, $group_id)
    {
        $group = Group::where('uuid', $group_id)->first();
        return $query->where('group_id', $group->id);
    }
}