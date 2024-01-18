<?php

namespace App\Models;

use App\Jobs\ProcessPortfolioVisuals;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Link extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;

    protected $fillable = [
        'uuid',
        'user_id',
        'label',
        'url',
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

    protected static function booted()
    {
         static::updated(function ($link) {

                if($link->label == 'portfolio'){
                    $new_url = $link->url;
                    Attachment::where('user_id', $link->user_id)->where('resource_type', 'website_preview')->delete();
                    ProcessPortfolioVisuals::dispatch($link->user_id, $new_url);
                }

            });
    }
}
