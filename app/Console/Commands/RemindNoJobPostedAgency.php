<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailJob;
use App\Models\Agency;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RemindNoJobPostedAgency extends Command
{
    protected $signature = 'remind-no-job-posted-agency';

    protected $description = "Remind's No Job Posted for Agency";

    public function handle()
    {
        $this->info($this->description);

        $date = today();
        if ($date->dayOfWeek >= Carbon::MONDAY && $date->dayOfWeek <= Carbon::FRIDAY) {
            try {

                $date_before = today()->subDays(5);

                $agency_user_ids = Agency::whereHas('user', function ($q) use ($date_before) {
                    $q->where('status', '=', 1)
                        ->where('role', '=', 3)
                        ->whereDate('created_at', '<=', $date_before);
                })->pluck('user_id')->toArray();

                $job_user_ids = Job::whereHas('user', function ($q) use ($date_before) {
                    $q->where('status', '=', 1)
                        ->where('role', '=', 3)
                        ->whereDate('created_at', '<=', $date_before);
                })->pluck('user_id')->toArray();

                $agency_user_ids = array_values(array_unique($agency_user_ids));
                $job_user_ids = array_values(array_unique($job_user_ids));

                Agency::whereIn('user_id', $job_user_ids)->update(['is_job_posted' => 1]);

                $agency_users_without_job_posts = array_values(array_unique(array_diff($agency_user_ids, $job_user_ids)));

                $agencies_without_job_posts = User::whereHas('agency', function ($q) use ($agency_users_without_job_posts) {
                    $q->whereIn('user_id', $agency_users_without_job_posts)
                        ->where('is_job_posted', '=', 0)
                        ->whereNull('job_posting_reminded_at');
                })
                    ->orderBy("created_at")
                    ->take(10)
                    ->get();

                $users_to_process = count($agencies_without_job_posts);
                $users_processed = 0;

                $this->info("Agencies to process: " . $users_to_process);

                foreach ($agencies_without_job_posts as $user) {
                    try {
                        $data = [
                            'data' => [
                                'first_name' => $user?->first_name ? $user->first_name : $user->username,
                                'profile_url' => sprintf('%s/agency/%s', env('FRONTEND_URL'), $user?->agency?->slug),
                            ],
                            'receiver' => $user,
                        ];
                        SendEmailJob::dispatch($data, 'no_job_posted_agency_reminder');
                        $agency = Agency::where('uuid', '=', $user?->agency->uuid)->first();
                        $agency->job_posting_reminded_at = today();
                        $agency->save();
                        $users_processed += 1;
                        $this->info("Reminded Agency: " . $user?->full_name . " - " . $user?->agency?->name);
                    } catch (\Throwable $th) {
                        $this->info("Failed Reminding Agency: " . $user?->full_name . " - " . $user?->agency?->name);
                    }
                }

                $this->info("Agencies processed: " . $users_processed);
            } catch (\Exception $e) {
                $this->info($e->getMessage());
            }
        } else {
            $this->info("Sorry, reminders are only meant to be sent between Monday to Friday only.");
        }
    }
}
