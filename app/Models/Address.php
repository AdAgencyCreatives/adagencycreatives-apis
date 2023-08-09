<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'label',
        'street_1',
        'street_2',
        'city',
        'state',
        'country',
        'postal_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
