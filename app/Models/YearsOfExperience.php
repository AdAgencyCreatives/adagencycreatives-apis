<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class YearsOfExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('years_of_experience');
        });

        static::updated(function ($experience) {
            Cache::forget('years_of_experience');
            $oldExperience = $experience->getOriginal('name');
            $newExperience = $experience->name;
            Creative::where('years_of_experience', $oldExperience)->update([
                'years_of_experience' => $newExperience
            ]);
        });

        static::deleted(function () {
            Cache::forget('years_of_experience');
        });
    }
}