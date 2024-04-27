<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLoggerTrait;


class Category extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'name',
        'group_name',
    ];

    public function creatives()
    {
        return $this->hasMany(Creative::class, 'category_id');
    }

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('all_categories');
        });

        static::updated(function () {
            Cache::forget('all_categories');
        });

        static::deleted(function () {
            Cache::forget('all_categories');
        });
    }
}