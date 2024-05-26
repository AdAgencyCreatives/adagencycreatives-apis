<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Job;
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
        $jobs = Job::where('apply_type', 'Internal')->where(function ($query) use ($yesterday, $today) {
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

            for ($j = 0; $j < count($job->applications); $j++) {
                $application = $job->applications[$j];

                $data[] = array(
                    'recipient_name' => $application->user->full_name,
                    'job_title' => $job->title,
                    'agency_name' => $job->agency_name ? $job->agency_name : $job->agency->name,
                );
            }
        }


        for ($k = 0; $k < count($data); $k++) {
            $item = $data[$k];


            SendEmailJob::dispatch([
                'receiver' => $item->receiver,
                'data' => [
                    'recipient' => $item->recipient_name,
                    'job_title' => $item->job_title,
                    'agency_name' => $item->agency_name,
                ],
            ], 'friendship_request_sent');
        }
    }
}
