<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'is_visible',
    ];

    protected $hidden = [
        'password',
    ];

    const ROLE_ADMIN = 1;

    const ROLE_ADVISOR = 2;

    const ROLE_AGENCY = 3;

    const ROLE_CREATIVE = 4;

    public function agency()
    {
        return $this->hasOne(Agency::class);
    }

    public function creative()
    {
        return $this->hasOne(Creative::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function getRoleAttribute($value)
    {
        switch ($value) {
            case self::ROLE_ADMIN:
                return 'admin';
            case self::ROLE_ADVISOR:
                return 'advisor';
            case self::ROLE_AGENCY:
                return 'agency';
            case self::ROLE_CREATIVE:
                return 'creative';
            default:
                return null;
        }
    }

    public function setRoleAttribute($value)
    {
        switch ($value) {
            case 'admin':
                $this->attributes['role'] = self::ROLE_ADMIN;
                break;
            case 'advisor':
                $this->attributes['role'] = self::ROLE_ADVISOR;
                break;
            case 'agency':
                $this->attributes['role'] = self::ROLE_AGENCY;
                break;
            default:
                $this->attributes['role'] = self::ROLE_CREATIVE;
                break;
        }
    }
}
