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
        'removed_from_recent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class)->withTrashed();
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
        return getApplicationStatus($value);
    }

    public function setStatusAttribute($value)
    {
        setApplicationStatus($this, $value);
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

            $job = Job::where('id', $application->job_id)->first();

            $author = User::find($job->user_id);
            $agency =  $author->agency; // fetch original agency for which we are posting
            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? $agency?->slug : '');

            if ($job->advisor_id) {
                $author = User::find($job->advisor_id); // author override only after original agency is fetched
            }

            $applicant = $application->user;

            if ($oldStatus == 'pending' && in_array($application->status, ['archived', 'rejected'])) {


                $data = [
                    'receiver' => $applicant->email,
                    'data' => [
                        'applicant' => $applicant->first_name ?? '',
                        'job_title' => $job->title ?? '',
                        'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                        'agency_name' => $agency_name ?? '',
                        'agency_profile' => strlen($agency_profile) > 0 ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile) : '',
                    ],

                ];
                create_notification($applicant->id, sprintf('Application rejected on "%s" job.', $job->title));
                SendEmailJob::dispatch($data, 'application_removed_by_agency');
            }

            if ($oldStatus == 'pending' && in_array($application->status, ['accepted'])) {

                $application_email_log = ApplicationEmailLog::where('application_id', '=', $application->id)
                    ->where('status', '=', getApplicationStatus($application->status))
                    ->whereDate('email_sent_at', today())->first();

                if (!$application_email_log) {
                    $data = [
                        'receiver' => $applicant->email,
                        'data' => [
                            'applicant' => $applicant->first_name ?? '',
                            'job_title' => $job->title ?? '',
                            'job_url' => sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug),
                            'agency_name' => $agency_name ?? '',
                            'agency_profile' => strlen($agency_profile) > 0 ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile) : '',
                        ],

                    ];
                    create_notification($applicant->id, sprintf('Application accepted on "%s" job.', $job->title));
                    SendEmailJob::dispatch($data, 'agency_is_interested');
                    ApplicationEmailLog::create([
                        'application_id' => $application->id,
                        'status' => $application->status,
                        'email_sent_at' => now(),
                    ]);
                }
            }
        });
    }
}
