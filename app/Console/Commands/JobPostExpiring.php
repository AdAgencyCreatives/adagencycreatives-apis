<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Job;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class JobPostExpiring extends Command
{
    protected $signature = 'job-post-expiring';

    protected $description = 'Send email with unread message counts to users';

    public function handle()
    {
        /**
         * Update expired jobs status
         */
        Job::where('expired_at', '<', now())->update([
            'status' => 3 //Expired
        ]);


        /**
         * Send Expiring jobs email to admin
         */
        $tomorrow = now()->addDay();

        $expiringTomorrowJobs = Job::whereDate('expired_at', $tomorrow)->get();
        foreach ($expiringTomorrowJobs as $job) {

            $author = User::find($job->user_id);
            $agency = $author->agency;

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
            $data = [
                'data' => [
                    'job_title' => $job->title,
                    'url' => $job_url,
                    'author' => $author->first_name,
                    'agency_name' => $agency->name ?? '',
                    'agency_profile' => sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency?->slug),
                    'created_at' => $job->created_at->format('M-d-Y'),
                ],
                'receiver' => User::where('email', env('ADMIN_EMAIL'))->first()
            ];
            SendEmailJob::dispatch($data, 'job_expiring_soon_admin');
            $data = [];
        }

        /**
         * Send Expiring jobs email to agency
         */
        $tomorrow = now()->addDays(3);

        $expiringTomorrowJobs = Job::where('status', 1)->whereDate('expired_at', $tomorrow)->get(); // only approved jobs which are not yet filled or closed.

        foreach ($expiringTomorrowJobs as $job) {

            $author = User::find($job->user_id);
            $agency = $author->agency;

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
            $data = [
                'data' => [
                    'job_title' => $job->title,
                    'url' => $job_url,
                    'author' => $author->first_name,
                    'agency_name' => $agency->name ?? '',
                    'agency_profile' => sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency?->slug),
                    'created_at' => $job->created_at->format('M-d-Y'),
                    'expired_at' => $job->expired_at->format('M-d-Y'),
                ],
                'receiver' => $author
            ];
            SendEmailJob::dispatch($data, 'job_expiring_soon_agency');
            $data = [];
        }
    }
}
