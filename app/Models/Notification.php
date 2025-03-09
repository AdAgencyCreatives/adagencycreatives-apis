<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'message',
        'body',
        'read_at',
        'sender_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'body' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSenderId(Builder $query, $sender_id)
    {
        $user = User::where('uuid', $sender_id)->firstOrFail();

        return $query->where('sender_id', $user->id);
    }

}