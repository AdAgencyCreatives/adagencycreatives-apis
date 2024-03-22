<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Category;
use App\Models\Job;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class SendUnreadJobAlertEmail extends Command
{
    protected $signature = 'email:unread-job-alert';

    protected $description = 'Send email with unread job notifications alert.';

    public function handle()
    {
        $date_range = now()->subDay();

        $unreadNotifications = Notification::whereDate('created_at', $date_range)
            ->where('type', 'job_alert')
            ->whereNull('read_at')
            ->get();


        foreach ($unreadNotifications as $notification) {

            $job = Job::find($notification->body['job_id']);

            $category = Category::find($job->category_id);
            $author = User::find($job->user_id);
            $agency = $author->agency;

            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? $agency?->slug : '');

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);
            $data = [
                'email_data' => [
                    'title' => $job->title ?? '',
                    'url' => $job_url,
                    'agency' => $agency_name,
                    'category' => $category?->name,
                    'user' => $author,
                    'agency_profile' => sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency_profile),
                ],
                'subscribers' => $author,
            ];

            SendEmailJob::dispatch($data, 'job_approved_alert_all_subscribers');
        }
    }
}
