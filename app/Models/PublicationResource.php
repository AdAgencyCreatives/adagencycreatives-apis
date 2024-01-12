<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class PublicationResource extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'link',
        'preview_link',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $maxSortOrder = static::max('sort_order') ?? 0;
            $model->sort_order = $maxSortOrder + 1;
        });
    }
}