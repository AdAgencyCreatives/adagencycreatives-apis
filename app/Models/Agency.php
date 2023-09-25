<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'about',
        'size',
        'industry_experience',
        'media_experience',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

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
}
