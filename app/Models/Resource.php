<?php

namespace App\Models;

use App\Jobs\ProcessMentorVisuals;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'link',
        'preview_link',
    ];

    protected $casts = [
        'id' => 'integer',
        'topic_id' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function scopeTopic(Builder $query, $topic_slug)
    {
        $topic = Topic::where('slug', $topic_slug)->first();
        if($topic) return $query->where('topic_id', $topic->id);

        return $query->where('topic_id', 0);
    }

    protected static function booted()
    {
        static::created(function ($resource) {
            $data = [
                'id' => $resource->id,
                'url' => $resource->link,
                'resource_type' => 'mentor_resource',
            ];
            ProcessMentorVisuals::dispatch($data);
        });

    }
}