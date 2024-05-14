<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
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
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\ActivityLoggerTrait;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use ActivityLoggerTrait;

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

    //protected $appends = array('full_name');


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
        'RECRUITER' => 5,
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

    public function user_thumbnail()
    {
        return $this->hasOne(Attachment::class)->where('resource_type', 'user_thumbnail')->latest();
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
        return $this->hasOne(Attachment::class)->where('resource_type', 'website_preview')->latest();
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
        return $this->hasOne(Attachment::class)->where('resource_type', 'resume')->latest();
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

    public function alert_categories()
    {
        return $this->belongsToMany(Category::class, 'job_alerts', 'user_id', 'category_id')->withTimestamps();
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
        $fullName = trim($this->first_name . ' ' . $this->last_name);

        return $fullName !== '' ? $fullName : $this->username;
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

    public function scopeAgencyName(Builder $query, $name): Builder
    {
        $agency = Agency::where('name', 'LIKE', "%$name%")->first();
        if ($agency) {
            return $query->where('id', $agency->user_id);
        } else {
            return $query->where('id', 0);
        }
    }

    public function scopeFirstName(Builder $query, $name): Builder
    {
        $name = explode(' ', $name);
        //if name is only one, then search in first name, if two then search seocnd term into last_name

        if (count($name) == 1) {
            return $query->where('first_name', $name[0])->orWhere('last_name', $name[0]);
        } else {
            return $query->where('first_name', $name[0])
                ->Where('last_name', $name[1])
                ->orWhere('first_name', $name[1])
                ->Where('last_name', $name[0]);
        }
    }

    public function scopeCategoryId(Builder $query, $uuid): Builder
    {
        $category = Category::where('uuid', $uuid)->first();
        $creative_ids = Creative::where('category_id', $category->id)->pluck('user_id');

        if ($creative_ids) {
            return $query->whereIn('id', $creative_ids);
        } else {
            return $query->where('id', 0);
        }
    }

    public function scopeStateId(Builder $query, $id): Builder
    {
        $state = Location::where('uuid', $id)->first();
        $creative_ids = Address::where('state_id', $state->id)->pluck('user_id');

        if ($creative_ids) {
            return $query->whereIn('id', $creative_ids);
        } else {
            return $query->where('id', 0);
        }
    }

    public function scopeCityId(Builder $query, $id): Builder
    {
        $city = Location::where('uuid', $id)->first();
        $creative_ids = Address::where('city_id', $city->id)->pluck('user_id');

        if ($creative_ids) {
            return $query->whereIn('id', $creative_ids);
        } else {
            return $query->where('id', 0);
        }
    }

    public function scopeIsFeatured(Builder $query, $value): Builder
    {
        $value = explode('_', $value);
        if ($value[0] == 'creative') {
            $creative_ids = Creative::where('is_featured', $value[1])->pluck('user_id');
            return $query->whereIn('id', $creative_ids);
        } else {
            $creative_ids = Agency::where('is_featured', $value[1])->pluck('user_id');
            return $query->whereIn('id', $creative_ids);
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
            case User::ROLES['RECRUITER']:
                return 'recruiter';
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
            case 'recruiter':
                $this->attributes['role'] = User::ROLES['RECRUITER'];
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
        if (!App::runningInConsole()) {
            static::created(function () {
                Cache::forget('users');
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts');
                Cache::forget('all_creatives');
                Cache::forget('get_users');
            });

            static::updating(function ($user) {
                if ($user->isDirty('email')) {
                    // Email address is being updated
                    $oldEmail = $user->getOriginal('email');
                    $newEmail = $user->email;
                    $data = [
                        'receiver' => $oldEmail,
                        'data' => [
                            'recipient' => $user->first_name,
                            'old_email' => $oldEmail,
                            'new_email' => $newEmail,
                        ]
                    ];
                    SendEmailJob::dispatch($data, 'email_updated');
                }

                if ($user->isDirty('role')) {
                    $newRole = $user->role;
                    if ($newRole == 'creative') {
                        Agency::where('user_id', $user->id)->delete();
                        Creative::onlyTrashed()->where('user_id', $user->id)->restore();

                        Address::where('user_id', $user->id)->where('label', 'business')->update([
                            'label' => 'personal'
                        ]);

                        Phone::where('user_id', $user->id)->where('label', 'business')->update([
                            'label' => 'personal'
                        ]);
                    } elseif (in_array($newRole, ['agency', 'advisor', 'recruiter'])) {
                        Creative::where('user_id', $user->id)->delete();
                        Agency::onlyTrashed()->where('user_id', $user->id)->restore();

                        Address::where('user_id', $user->id)->where('label', 'personal')->update([
                            'label' => 'business'
                        ]);

                        Phone::where('user_id', $user->id)->where('label', 'personal')->update([
                            'label' => 'business'
                        ]);
                    }

                    Artisan::call('optimize:clear');
                }
            });
            static::updated(function ($user) {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts');
                Cache::forget('all_users_with_attachments');
                Cache::forget('all_creatives');
                Cache::forget('get_users');

                if ($user->isDirty('username')) {
                    //Update slug in creatives table when username is changes, slug in creatives table fallbacks to username
                    tap($user, function ($user) {
                        if ($user->creative) {
                            $user->creative->update(['slug' => Str::slug($user->username)]);
                        }

                        if ($user->agency) {
                            $user->agency->update(['slug' => Str::slug($user->username)]);
                        }
                    });
                }
            });

            static::deleted(function ($user) {
                Cache::forget('dashboard_stats_cache');
                Cache::forget('all_users_with_posts'); //cache for displaying count of posts on admin dashboard for posts page
                Cache::forget('all_users_with_attachments'); //cache for displaying count of attachments on admin dashboard for Media page
                Cache::forget('all_creatives'); //cache for displaying list of creatives Add Creative Spotlight page
                Cache::forget('get_users'); //cache for displaying list of creatives Add Creative Spotlight page

                if ($user->role == 'creative') {
                    Creative::where('user_id', $user->id)->delete();
                    Education::where('user_id', $user->id)->delete();
                    Experience::where('user_id', $user->id)->delete();
                    PostReaction::where('user_id', $user->id)->delete();
                } elseif ($user->role == 'agency') {
                    Agency::where('user_id', $user->id)->delete();
                }

                Link::where('user_id', $user->id)->delete();
                Attachment::where('user_id', $user->id)->delete();
                Address::where('user_id', $user->id)->delete();
                Phone::where('user_id', $user->id)->delete();
                Bookmark::where('user_id', $user->id)->delete();
                Review::where('user_id', $user->id)->delete();

                // Delete all the user Groups, when group is deleted, all posts will be deleted
                Group::where('user_id', $user->id)->delete();
            });
        }
    }
}