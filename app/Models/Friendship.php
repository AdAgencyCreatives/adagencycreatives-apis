<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

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
}