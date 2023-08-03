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
        'is_visible'
    ];

    protected $hidden = [
        'password'
    ];

    public function agency(){
        return $this->hasMany(Agency::class);
    }
    
    public function creative(){
        return $this->hasMany(Creative::class);
    }

    public function phones(){
        return $this->hasMany(Phone::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    public function links(){
        return $this->hasMany(Link::class);
    }

    public function jobs(){
        return $this->hasMany(Job::class);
    }


}
