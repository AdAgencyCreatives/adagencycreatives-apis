<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class Friendship extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'user1_id',
        'user2_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function initiatedByUser()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    // Relationship for the user who received the friendship (user2)
    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    protected static function booted()
    {
        static::deleted(function ($friendship) {
            $friendship = FriendRequest::where('sender_id', $friendship->user1_id)
                ->where('receiver_id', $friendship->user2_id)->update([
                    'status' => 'unfriended',
                ]);
        });

    }
}