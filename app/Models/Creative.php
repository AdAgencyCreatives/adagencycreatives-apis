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

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        if ($user) {
            return $query->where('user_id', $user->id);
        }
    }

    public function scopeYearsOfExperienceId(Builder $query, $exp_id)
    {
        $experience = YearsOfExperience::where('uuid', $exp_id)->first();

        return $query->where('years_of_experience', $experience->name);

    }
}
