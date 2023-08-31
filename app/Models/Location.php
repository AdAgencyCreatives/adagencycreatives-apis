<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'parent_id',
    ];

    public function scopeStateId(Builder $query, $state_id)
    {
        $state_id = Location::where('uuid', $state_id)->pluck('id');

        return $query->where('parent_id', $state_id);
    }
}
