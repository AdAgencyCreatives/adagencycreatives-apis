<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'topic_id',
        'description',
        'link',
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
}