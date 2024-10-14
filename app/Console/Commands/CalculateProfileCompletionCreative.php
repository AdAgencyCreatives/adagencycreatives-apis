<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailJob;
use App\Models\Creative;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CalculateProfileCompletionCreative extends Command
{
    protected $signature = 'calculate-profile-completion-creative';

    protected $description = "Calculates Profile Completion for Creative";

    public function handle()
    {

        try {
            $this->info($this->description);

            $creatives = Creative::whereHas('user', function ($q) {
                $q->where('role', '=', 4)->orderBy('created_at');
            })->get();



            $users_to_process = count($creatives);
            $users_processed = 0;

            $this->info("Creatives to process: " . $users_to_process);

            foreach ($creatives as $creative) {
                if (!$creative->user) {
                    continue;
                }
                try {
                    $progress = $this->getCreativeProfileProgress($creative);
                    $user = User::where('uuid', '=', $creative->user->uuid)->first();
                    $user->profile_complete_progress = $progress;
                    $user->profile_completed_at = $progress == 100 ? today() : null;
                    $user->save();
                    $users_processed += 1;
                    $this->info("Calculate Profile Completion Success for Creative: " . $user?->full_name);
                } catch (\Exception $e2) {
                    $this->info("Calculate Profile Completion Failed for Creative: " . $user?->full_name);
                }
            }

            $this->info("Creatives processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
