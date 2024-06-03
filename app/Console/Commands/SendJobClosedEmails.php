<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Category;
use App\Models\Job;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendJobClosedEmails extends Command
{

    protected $signature = 'email:send-job-closed-emails';
    protected $description = 'It will send job closed emails to users who have applied on internal jobs.';

    public function handle()
    {
        $yesterday = now()->subDay()->toDateString();
        $today = now()->toDateString();

        $this->info('From: ' . $yesterday);
        $this->info('To: ' . $today);

        $jobs = Job::where(function ($query) use ($yesterday, $today) {
            $query->where(function ($q) use ($yesterday, $today) {
                $q->where('status', 4)->whereDate('updated_at', '>=', $yesterday)->where('updated_at', '<', $today);
            })->orWhere(function ($q) use ($yesterday, $today) {
                $q->whereDate('expired_at', '>=', $yesterday)->where('expired_at', '<', $today);
            });
        })->with('applications', function ($query) {
            $query->where('status', 0);
        })->get();

        $data = [];
        for ($i = 0; $i < count($jobs); $i++) {
            $job = $jobs[$i];

            $author = User::find($job->user_id);
            $agency = $author->agency;

            $agency_name = $job?->agency_name ?? ($agency?->name ?? '');
            $agency_profile = $job?->agency_website ?? (in_array($author->role, ['agency']) ? sprintf("%s/agency/%s", env('FRONTEND_URL'), $agency?->slug) : '');

            $job_url = sprintf('%s/job/%s', env('FRONTEND_URL'), $job->slug);

            for ($j = 0; $j < count($job->applications); $j++) {
                $application = $job->applications[$j];

                $data[] = array(
                    'receiver' =>  $application->user->email,
                    'recipient_name' => $application->user->first_name,
                    'job_title' => $job->title,
                    'job_url' => $job_url,
                    'agency_name' => $agency_name,
                    'agency_profile' => $agency_profile,
                    'apply_type' => $job->apply_type,
                    'show_test_links'=>'no'
                );
            }
        }

        $this->info("Sending Email to " . count($data) . ' Applicants');

        for ($k = 0; $k < count($data); $k++) {
            $item = $data[$k];

            SendEmailJob::dispatch([
                'receiver' => $item['receiver'],
                'data' => [
                    'recipient_name' => $item['recipient_name'],
                    'job_title' => $item['job_title'],
                    'job_url' => $item['job_url'],
                    'agency_name' => $item['agency_name'],
                    'agency_profile' => $item['agency_profile'],
                    'apply_type' => $item['apply_type'],
                    'show_test_links' => $item['show_test_links'],
                ],
            ], 'job_closed_email');
        }
    }
}