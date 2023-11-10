<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'group_id',
        'role',
        'joined_at',
    ];

    protected $dates = [
        'joined_at',
    ];

    const ROLES = [
        'ADMIN' => 1,
        'MODERATOR' => 2,
        'MEMBER' => 3,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleAttribute($value)
    {
        switch ($value) {
            case GroupMember::ROLES['ADMIN']:
                return 'Admin';
            case GroupMember::ROLES['MODERATOR']:
                return 'Moderator';
            case GroupMember::ROLES['MEMBER']:
                return 'Member';
            default:
                return null;
        }
    }

    public function setRoleAttribute($value)
    {
        switch ($value) {
            case 'admin':
                $this->attributes['role'] = GroupMember::ROLES['ADMIN'];
                break;
            case 'moderator':
                $this->attributes['role'] = GroupMember::ROLES['MODERATOR'];
                break;
            default:
                $this->attributes['role'] = GroupMember::ROLES['MEMBER'];
                break;
        }
    }


    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();
        return $query->where('user_id', $user->id);
    }


    public function scopeGroupId(Builder $query, $group_id): Builder
    {
        $group = Group::where('uuid', $group_id)->first();
        return $query->where('group_id', $group->id);
    }
}
