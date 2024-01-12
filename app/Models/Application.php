<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ActivityLoggerTrait;

class Application extends Model
{
    use HasFactory, SoftDeletes;
    use ActivityLoggerTrait;


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
        'ARCHIVED' => 3, // Application will remove from agency frontend, but it will still exist in the database, so that candidate can't submit the application again.
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
            case Application::STATUSES['ARCHIVED']:
                return 'archived';

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
            case 'archived':
                $this->attributes['status'] = Application::STATUSES['ARCHIVED'];
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

    protected static function booted()
    {
        static::updating(function ($application) {

            $oldStatus = $application->getOriginal('status');
            if ($oldStatus == 'pending' && in_array($application->status, ['archived', 'rejected'])) {

                $job = Job::where('id', $application->job_id)->first();
                $applicant = $application->user;
                $data = [
                    'receiver' => $applicant,
                    'data' => [
                        'applicant' => $applicant->first_name ?? '',
                        'job_title' => $job->title ?? '',
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                    ],

                ];
                create_notification($applicant->id, sprintf('Application rejected on "%s" job.', $job->title));
                SendEmailJob::dispatch($data, 'application_removed_by_agency');
            }

            if ($oldStatus == 'pending' && in_array($application->status, ['accepted'])) {

                $job = Job::where('id', $application->job_id)->first();
                $agency = $job->agency;
                $applicant = $application->user;

                $data = [
                    'receiver' => $applicant,
                    'data' => [
                        'applicant' => $applicant->first_name ?? '',
                        'job_title' => $job->title ?? '',
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                        'agency_name' => $agency->name ?? '',
                    ],

                ];
                create_notification($applicant->id, sprintf('Application accepted on "%s" job.', $job->title));
                SendEmailJob::dispatch($data, 'agency_is_interested');
            }

        });
    }
}