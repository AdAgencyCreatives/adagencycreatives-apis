<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggerTrait;
use Illuminate\Support\Facades\Cache;

class Creative extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'title',
        'slug',
        'about',
        'employment_type',
        'years_of_experience',
        'industry_experience',
        'media_experience',
        'strengths',
        'is_hybrid',
        'is_onsite',
        'is_remote',
        'is_featured',
        'is_urgent',
        'is_opentorelocation',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'views',
        'featured_at',
        'is_welcomed',
        'welcomed_at',
        'welcome_queued_at',
        'sort_order',
    ];

    protected $casts = [
        'featured_at' => 'datetime',
        'welcomed_at' => 'datetime',
        'welcome_queued_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function profile_picture()
    {
        return $this->hasOne(Attachment::class, 'resource_id', 'id')->where('resource_type', 'profile_picture');
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();

        return $query->where('user_id', $user->id);
    }

    public function scopeEmail(Builder $query, $email)
    {
        $user = User::where('email', $email)->first();

        return $query->where('user_id', $user->id);
    }

    public function scopeName(Builder $query, $name)
    {
        $user_ids = User::whereRaw("CONCAT(first_name,' ', last_name) LIKE '%" . $name . "%'")->pluck('id');

        return $query->whereIn('user_id', $user_ids);
    }

    public function scopeStateId(Builder $query, $state_id)
    {
        $location = Location::with('states')->where('uuid', $state_id)->first();

        return $query->whereIn('user_id', $location->states->pluck('user_id'));
    }

    public function scopeCityId(Builder $query, $city_id)
    {
        $location = Location::with('cities')->where('uuid', $city_id)->first();

        return $query->whereIn('user_id', $location->cities->pluck('user_id'));
    }

    public function scopeYearsOfExperienceId(Builder $query, $exp_id)
    {
        $experience = YearsOfExperience::where('uuid', $exp_id)->first();

        return $query->where('years_of_experience', $experience->name);
    }

    public function scopeStatus(Builder $query, $status)
    {
        $user_ids = User::where('status', $status)->pluck('id');

        return $query->whereIn('user_id', $user_ids);
    }

    public function scopeIsVisible(Builder $query, $is_visible)
    {
        $user_id = User::where('is_visible', $is_visible)->pluck('id');

        return $query->whereIn('user_id', $user_id);
    }

    public function scopeNotInGroup(Builder $query, $groupId)
    {
        return $query->whereNotIn('user_id', function ($subQuery) use ($groupId) {
            $subQuery->select('user_id')
                ->from('group_members')
                ->where('group_id', $groupId);
        });
    }

    public function scopeNotInvited(Builder $query, $groupId)
    {
        return $query->whereNotIn('user_id', function ($subQuery) use ($groupId) {
            $subQuery->select('invitee_user_id')
                ->from('group_invitations')
                ->where('group_id', $groupId);
        });
    }

    protected static function booted()
    {
        static::created(function ($creative) {
            if ($creative->slug == null) {
                $creative->slug = Str::slug($creative->user->username);
                $creative->save();
            }
        });

        static::updated(function ($creative) {
            if ($creative->isDirty(['is_featured', 'featured_at'])) {
                Cache::forget('homepage_creatives');
            }
        });
    }
}