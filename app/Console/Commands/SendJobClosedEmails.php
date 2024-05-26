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
        $jobs = Job::withCount('applications')->where('apply_type', 'Internal')->where(function ($query) use ($yesterday, $today) {
            $query->where(function ($q) use ($yesterday, $today) {
                $q->where('status', 4)->whereDate('updated_at', '>=', $yesterday)->where('updated_at', '<', $today);
            })->orWhere(function ($q) use ($yesterday, $today) {
                $q->whereDate('expired_at', '>=', $yesterday)->where('expired_at', '<', $today);
            });
        })->get();

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];

            SendEmailJob::dispatch([
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'senders' => $senders,
                    'multiple' => count($senders) > 1 ? "yes" : "no",
                ],
            ], 'friendship_request_sent');
        }
    }
}