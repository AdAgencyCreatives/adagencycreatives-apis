<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('dashboard_stats_cache');
        });
    }
}
