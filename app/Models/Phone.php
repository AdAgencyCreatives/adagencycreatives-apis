<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function setPhoneNumberAttribute($value)
    {
        // Remove non-numeric characters from the phone number
        $cleanedNumber = preg_replace('/[^0-9]/', '', $value);

        // Format the phone number as xxx-xxx-xxxx
        $formattedNumber = substr($cleanedNumber, 0, 3) . '-' . substr($cleanedNumber, 3, 3) . '-' . substr($cleanedNumber, 6);

        // Set the formatted phone number attribute
        $this->attributes['phone_number'] = $formattedNumber;
    }
}