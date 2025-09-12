<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class Subscription extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'quantity',
        'quota_left',
        'ends_at',
    ];

    protected $casts = [
        'ends_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'name', 'slug');
    }

    public function monthlyQuota()
    {
        return $this->hasOne(MonthlyQuota::class);
    }
}
