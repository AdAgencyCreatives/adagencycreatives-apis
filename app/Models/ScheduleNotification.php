<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'sender_id',
        'recipient_id',
        'post_id',
        'notification_text',
        'type',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public const STATUSES = [
        'PENDING' => 0,
        'DELIVERED' => 1,
    ];

    public const TYPES = [
        'CREATE_POST' => 0,
        'COMMENT' => 1,
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function scopeSenderId(Builder $query, $sender_id): Builder
    {
        $sender = User::where('uuid', $sender_id)->first();

        return $query->where('sender_id', $sender->id);
    }

    public function scopeRecipientId(Builder $query, $recipient_id): Builder
    {
        $recipient = User::where('uuid', $recipient_id)->first();

        return $query->where('recipient_id', $recipient->id);
    }

    public function scopePostId(Builder $query, $post_id): Builder
    {
        $post = Post::where('uuid', $post_id)->first();

        return $query->where('post_id', $post->id);
    }

    public function getStatusAttribute($value)
    {
        $value = (int) $value;
        return match ($value) {
            ScheduleNotification::STATUSES['DELIVERED'] => 'delivered',
            default => 'pending',
        };
    }

    public function setStatusAttribute($value)
    {
        $value = (int) $value;
        return match ($value) {
            ScheduleNotification::STATUSES['DELIVERED'] => $this->attributes['status'] = ScheduleNotification::STATUSES['DELIVERED'],
            default => $this->attributes['status'] = ScheduleNotification::STATUSES['PENDING']
        };
    }

    public function getTypeAttribute($value)
    {
        $value = (int) $value;
        return match ($value) {
            ScheduleNotification::TYPES['COMMENT'] => 'comment',
            default => 'create_post',
        };
    }

    public function setTypeAttribute($value)
    {
        $value = (int) $value;
        match ($value) {
            ScheduleNotification::TYPES['COMMENT'] => $this->attributes['type'] = ScheduleNotification::TYPES['COMMENT'],
            default => $this->attributes['type'] = ScheduleNotification::TYPES['CREATE_POST']
        };
    }
}
