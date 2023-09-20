<?php

namespace App\Models;

use App\Jobs\SendResetPasswordJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    // protected $primaryKey = 'uuid';

    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'is_visible',
    ];

    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    public function sendPasswordResetNotification($token)
    {
        SendResetPasswordJob::dispatch($token, $this);
    }

    protected $hidden = [
        'password',
    ];

    public const ROLES = [
        'ADMIN' => 1,
        'ADVISOR' => 2,
        'AGENCY' => 3,
        'CREATIVE' => 4,
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'ACTIVE' => 1,
        'INACTIVE' => 2,
    ];

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

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function resume()
    {
        return $this->hasOne(Resume::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'target_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function getRoleAttribute($value)
    {
        switch ($value) {
            case User::ROLES['ADMIN']:
                return 'admin';
            case User::ROLES['ADVISOR']:
                return 'advisor';
            case User::ROLES['AGENCY']:
                return 'agency';
            case User::ROLES['CREATIVE']:
                return 'creative';
            default:
                return null;
        }
    }

    public function setRoleAttribute($value)
    {
        switch ($value) {
            case 'admin':
                $this->attributes['role'] = User::ROLES['ADMIN'];
                break;
            case 'advisor':
                $this->attributes['role'] = User::ROLES['ADVISOR'];
                break;
            case 'agency':
                $this->attributes['role'] = User::ROLES['AGENCY'];
                break;
            default:
                $this->attributes['role'] = User::ROLES['CREATIVE'];
                break;
        }
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case User::STATUSES['PENDING']:
                return 'pending';
            case User::STATUSES['ACTIVE']:
                return 'active';
            case User::STATUSES['INACTIVE']:
                return 'inactive';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'active':
                $this->attributes['status'] = User::STATUSES['ACTIVE'];
                break;
            case 'inactive':
                $this->attributes['status'] = User::STATUSES['INACTIVE'];
                break;
            default:
                $this->attributes['status'] = User::STATUSES['PENDING'];
                break;
        }
    }

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('users');
            Cache::forget('dashboard_stats_cache');
            Cache::forget('all_users');
        });

        static::updated(function ($user) {
            Cache::forget("user:$user->id");
            Cache::forget('dashboard_stats_cache');
            Cache::forget('all_users');
        });

        static::deleted(function ($user) {
            Cache::forget("user:$user->id");
            Cache::forget('dashboard_stats_cache');
            Cache::forget('all_users');
        });
    }
}
