<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteError extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'url',
        'error_message',
        'email_sent_at',
    ];

    protected static function booted() {}
}