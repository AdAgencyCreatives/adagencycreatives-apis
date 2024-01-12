<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityLoggerTrait;

class Note extends Model
{
    use HasFactory;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'notable_type',
        'notable_id',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notable()
    {
        return $this->morphTo();
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeUserId(Builder $query, $user_id): Builder
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);
    }

    public function scopeResourceType(Builder $query, $resource): Builder
    {
        $resource = Bookmark::$modelAliases[$resource] ?? null;

        return $query->where('notable_type', $resource);
    }

    public function scopeResourceId(Builder $query, $resource): Builder
    {
        $resource = Bookmark::$modelAliases[$resource] ?? null;

        return $query->where('notable_type', $resource);
    }

    public function scopeApplicationId(Builder $query, $app_id)
    {
        $application = Application::where('uuid', $app_id)->firstOrFail();

        return $query->where('application_id', $application->id);
    }
}
