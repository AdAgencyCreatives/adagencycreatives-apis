<?php

namespace App\Models;

use App\Jobs\SendResetPasswordJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Stripe\Subscription as StripeSubscription;

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

    public function profile_picture()
    {
        return $this->hasOne(Attachment::class)->where('resource_type', 'profile_picture')->latest();
    }

    public function agency_logo()
    {
        return $this->hasOne(Attachment::class)->where('resource_type', 'agency_logo')->latest();
    }

    public function portfolio_spotlights()
    {
        return $this->hasMany(Attachment::class)->where('resource_type', 'creative_spotlight');
    }

    public function portfolio_items() //upto 5 items
    {
        return $this->hasMany(Attachment::class)->where('resource_type', 'portfolio_item');
    }

    public function portfolio_item_links() //upto 5 items
    {
        return $this->hasMany(Link::class)->where('label', 'portfolio');
    }

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

    public function open_jobs()
    {
        return $this->hasMany(Job::class)->where('status', Job::STATUSES['APPROVED'])->count();
    }

    public function alert()
    {
        return $this->hasOne(JobAlert::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscription()
    {
        return $this->hasOne(StripeSubscription::class);
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
        if (! App::runningInConsole()) {
            static::created(function () {
                Cache::forget('users');
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts');
            });

            static::updated(function () {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts');
                Cache::forget('all_users_with_attachments');
            });

            static::deleted(function () {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts'); //cache for displaying count of posts on admin dashboard for posts page
                Cache::forget('all_users_with_attachments'); //cache for displaying count of attachments on admin dashboard for Media page
            });
        }

    }
}
