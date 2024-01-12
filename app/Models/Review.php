<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Review extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'target_id',
        'comment',
        'rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function scopeTargetId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();

        return $query->where('target_id', $user->id);

    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->first();
        return $query->where('user_id', $user->id);
    }
}