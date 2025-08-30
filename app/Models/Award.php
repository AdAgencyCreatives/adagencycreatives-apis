<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Award extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $table = 'educations';

    protected $fillable = [
        'uuid',
        'user_id',
        'award_title',
        'award_year',
        'award_work',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }
}