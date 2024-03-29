<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class JobAlert extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'category_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->first();

        if($user) return $query->where('user_id', $user->id);
        return $query->where('user_id', 0);
    }
}
