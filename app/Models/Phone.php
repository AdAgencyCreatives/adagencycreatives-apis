<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'label',
        'country_code',
        'phone_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
