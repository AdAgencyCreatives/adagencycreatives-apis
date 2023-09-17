<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id')->select('id', 'uuid', 'first_name', 'last_name', 'email');
    }
}
