<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
    ];

    // We will store full namespace for relevant models
    public static $modelAliases = [
        'creatives' => Creative::class,
        'agencies' => Agency::class,
        'jobs' => Job::class,
        'applications' => Application::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function scopeResourceType(Builder $query, $resource): Builder
    {
        $resource = Bookmark::$modelAliases[$resource] ?? null;

        return $query->where('bookmarkable_type', $resource);
    }

    public static function getIdByUUID($modelClass, $uuid)
    {
        try {
            return $modelClass::where('uuid', $uuid)->firstOrFail()->getKey();
        } catch (\Exception $e) {
            return null;
        }
    }
}
