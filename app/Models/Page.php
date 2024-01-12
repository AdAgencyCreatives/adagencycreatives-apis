<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class Page extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'page',
        'key',
        'value',
    ];
}