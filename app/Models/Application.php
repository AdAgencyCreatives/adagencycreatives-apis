<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'job_id',
        'attachment_id',
        'message',
        'status',
    ];

    const STATUSES = [
        'PENDING' => 0,
        'ACCEPTED' => 1,
        'REJECTED' => 2,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function getStatusAttribute($value)
    {
        switch ($value) {
            case Application::STATUSES['PENDING']:
                return 'pending';
            case Application::STATUSES['ACCEPTED']:
                return 'accepted';
            case Application::STATUSES['REJECTED']:
                return 'rejected';

            default:
                return null;
        }
    }

    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'accepted':
                $this->attributes['status'] = Application::STATUSES['ACCEPTED'];
                break;
            case 'rejected':
                $this->attributes['status'] = Application::STATUSES['REJECTED'];
                break;
            default:
                $this->attributes['status'] = Application::STATUSES['PENDING'];
                break;
        }
    }

    public function scopeUserId(Builder $query, $user_id)
    {
        $user = User::where('uuid', $user_id)->firstOrFail();

        return $query->where('user_id', $user->id);

    }

    public function scopeJobId(Builder $query, $job_id)
    {
        $job = Job::where('uuid', $job_id)->first();
        if ($job) {
            return $query->where('job_id', $job->id);
        }
    }
}
