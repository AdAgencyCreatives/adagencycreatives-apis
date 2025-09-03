<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'order',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('all_articles');
        });

        static::updated(function () {
            Cache::forget('all_articles');
        });

        static::deleted(function () {
            Cache::forget('all_articles');
        });
    }
}