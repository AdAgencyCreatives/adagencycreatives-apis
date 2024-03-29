<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;

class Festival extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'title',
        'path',
        'category',
        'version',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('festival_creatives');
        });

        static::updated(function () {
            Cache::forget('festival_creatives');
        });

        static::deleted(function () {
            Cache::forget('festival_creatives');
        });
    }

}