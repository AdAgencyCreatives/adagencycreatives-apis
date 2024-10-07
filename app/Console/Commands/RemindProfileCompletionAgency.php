<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailJob;
use App\Models\Agency;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RemindProfileCompletionAgency extends Command
{
    protected $signature = 'remind-profile-completion-creative';

    protected $description = "Remind's Profile Completion for Agency";

    public function handle()
    {

        try {
            $this->info($this->description);

            $users = User::where('role', '=', 3)
                ->whereNull('profile_completed_at')
                ->whereNull('profile_completion_reminded_at')
                ->orderBy('created_at')
                ->take(10)
                ->get();

            $users_to_process = count($users);
            $users_processed = 0;

            $this->info("Agencies to process: " . $users_to_process);

            try {
                foreach ($users as $user) {
                    $data = [
                        'data' => [
                            'first_name' => $user?->first_name ?? '',
                        ],
                        'receiver' => $user,
                    ];
                    SendEmailJob::dispatch($data, 'profile_completion_agency');
                    $user->profile_completion_reminded_at = today();
                    $user->save();
                    $users_processed += 1;
                }
            } catch (\Throwable $th) {
            }

            $this->info("Agencies processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}