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

class CalculateProfileCompletionAgency extends Command
{
    protected $signature = 'calculate-profile-completion-agency';

    protected $description = "Calculates Profile Completion for Agency";

    public function handle()
    {

        try {
            $this->info($this->description);

            $agencies = Agency::whereHas('user', function ($q) {
                $q->where('role', '=', 3)->orderBy('created_at');
            })->get();



            $users_to_process = count($agencies);
            $users_processed = 0;

            $this->info("Agencies to process: " . $users_to_process);

            foreach ($agencies as $agency) {
                if (!$agency->user) {
                    continue;
                }
                try {
                    $progress = $this->getAgencyProfileProgress($agency);
                    $user = User::where('uuid', '=', $agency->user->uuid)->first();
                    $user->profile_complete_progress = $progress;
                    $user->profile_completed_at = $progress == 100 ? today() : null;
                    $user->save();
                    $users_processed += 1;
                    $this->info("Calculate Profile Completion Success for Agency: " . $user?->full_name);
                } catch (\Exception $e2) {
                    $this->info("Calculate Profile Completion Failed for Agency: " . $user?->full_name);
                }
            }

            $this->info("Agencies processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
