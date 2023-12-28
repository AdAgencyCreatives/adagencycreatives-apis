<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }


    protected static function booted()
    {
        static::created(function ($topic) {
            $topic->slug = $topic->slug = Str::slug($topic->slug ?? $topic->title);
            $topic->save();
            Cache::forget('homepage_mentor_topics');
        });

        static::updated(function () {
            Cache::forget('homepage_mentor_topics');
        });

        static::deleted(function () {
            Cache::forget('homepage_mentor_topics');

        });
    }
}
