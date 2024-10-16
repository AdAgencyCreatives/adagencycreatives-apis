<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\ActivityLoggerTrait;

class Agency extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'slug',
        'about',
        'size',
        'industry_experience',
        'media_experience',
        'is_hybrid',
        'is_onsite',
        'is_remote',
        'is_featured',
        'is_urgent',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'views',
        'featured_at',
    ];

    protected $casts = [
        'featured_at' => 'datetime',
    ];

    public function getApprovedJobCountAttribute()
    {
        // Access the 'open_jobs' relationship through the 'user' relationship
        return $this->user->open_jobs()->count();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->hasOne(Attachment::class, 'resource_id');
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();

        return $query->where('user_id', $user->id);
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

    public function scopeRole(Builder $query, $role)
    {
        $users_ids = User::where('role', $role)->pluck('id');
        return $query->whereIn('user_id', $users_ids);
    }

    protected static function booted()
    {

        static::created(function ($agency) {
            if ($agency->slug == null) {
                $agency->slug = Str::slug($agency->user->username);
                $agency->save();
            }
        });
    }
}