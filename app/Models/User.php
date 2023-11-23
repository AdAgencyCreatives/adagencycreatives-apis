<?php

namespace App\Models;

use App\Jobs\SendResetPasswordJob;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
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

    public function portfolio_website_link()
    {
        return $this->hasOne(Link::class)->where('label', 'portfolio');
    }

    public function portfolio_website_preview()
    {
        return $this->hasOne(Attachment::class)->where('resource_type', 'website_preview');
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

    public function personal_phone()
    {
        return $this->hasOne(Phone::class)->where('label', 'personal');
    }

    public function business_phone()
    {
        return $this->hasOne(Phone::class)->where('label', 'business');
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

    // public function resume()
    // {
    //     return $this->hasOne(Resume::class);
    // }

    public function resume()
    {
        return $this->hasOne(Attachment::class)->where('resource_type', 'resume')->latestOfMany();
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

    public function active_subscription()
    {
        return $this->hasOne(Subscription::class)
            ->latestOfMany();
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function latest_subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
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

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Community Relations
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user1_id', 'user2_id');
    }

    public function friendRequestsSent()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function friendRequestsReceived()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function scopeCompanySlug(Builder $query, $company_slug): Builder
    {
        $agency = Agency::where('slug', $company_slug)->first();
        if ($agency) {
            return $query->where('id', $agency->user_id);
        } else {
            return $query->where('id', 0);
        }
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
                Cache::forget('all_creatives');
            });

            static::updated(function ($user) {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts');
                Cache::forget('all_users_with_attachments');
                Cache::forget('all_creatives');

                //Update slug in creatives table when username is changes, slug in creatives table fallbacks to username
                if ($user->creative) {
                    $creative = $user->creative;
                    $creative->slug = Str::slug($user->username);
                    $creative->save();
                }
            });

            static::deleted(function ($user) {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts'); //cache for displaying count of posts on admin dashboard for posts page
                Cache::forget('all_users_with_attachments'); //cache for displaying count of attachments on admin dashboard for Media page
                Cache::forget('all_creatives'); //cache for displaying list of creatives Add Creative Spotlight page

                // Delete all the user Groups, when group is deleted, all posts will be deleted
                Group::where('user_id', $user->id)->delete();
            });
        }

    }
}
