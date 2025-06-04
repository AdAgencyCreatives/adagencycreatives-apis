<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
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
            Cache::forget('all_faqs');
        });

        static::updated(function () {
            Cache::forget('all_faqs');
        });

        static::deleted(function () {
            Cache::forget('all_faqs');
        });
    }
}