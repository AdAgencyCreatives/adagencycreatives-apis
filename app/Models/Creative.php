<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Creative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'about',
        'employment_type',
        'years_of_experience',
        'industry_experience',
        'media_experience',
        'strengths',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function scopeName(Builder $query, $name)
    {
        $user_ids = User::where('first_name', $name)->orWhere('last_name', $name)->pluck('id');

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
}
