<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'resource_type',
        'resource_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
