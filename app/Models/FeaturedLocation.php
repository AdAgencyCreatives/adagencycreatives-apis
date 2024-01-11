<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'preview_link',
        'sort_order',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}