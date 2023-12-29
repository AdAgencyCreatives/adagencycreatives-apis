<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
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