<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'name',
        'attachment_id',
        'about',
        'size',
        'type_of_work',
        'industry_specialty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
