<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Award extends Model
{
    use HasFactory, SoftDeletes, ActivityLoggerTrait;

    protected $table = 'awards';

    protected $fillable = [
        'uuid',
        'user_id',
        'award_title',
        'award_year',
        'award_work',
    ];

    /**
     * Relationship: Award belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Filter awards by User UUID
     */
    public function scopeUserId(Builder $query, $userUuid): Builder
    {
        return $query->whereHas('user', function ($q) use ($userUuid) {
            $q->where('uuid', $userUuid);
        });
    }
}
