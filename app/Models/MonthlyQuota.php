<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'jobs_posted_this_month',
        'last_reset_at',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
