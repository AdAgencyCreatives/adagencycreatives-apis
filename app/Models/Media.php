<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;

class Media extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $table = 'medias';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
    ];

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('all_medias');
        });

        static::updated(function () {
            Cache::forget('all_medias');
        });

        static::deleted(function () {
            Cache::forget('all_medias');
        });
    }
}
