<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resume extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'years_of_experience',
        'about',
        'industry_specialty',
        'media_experience',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
