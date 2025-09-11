<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Article extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'title',
        'sub_title',
        'order',
        'article_date',
        'description',
        'is_featured',
        'featured_at',
    ];

    protected $casts = [
        'article_date' => 'datetime',
        'is_featured' => 'boolean',
        'featured_at' => 'datetime',
        'order' => 'integer'
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
