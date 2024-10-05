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

class RemindProfileCompletionCreative extends Command
{
    protected $signature = 'remind-profile-completion-creative';

    protected $description = "Remind's Profile Completion for Creative";

    public function handle()
    {

        try {
            $this->info($this->description);

            $creatives = Creative::whereNull('profile_completed_at')
                ->whereNull('profile_completion_reminded_at')
                ->orderBy('created_at')
                ->take(30)
                ->get();

            $creatives_to_process = count($creatives);
            $creatives_processed = 0;

            $this->info("Creatives to process: " . $creatives_to_process);

            try {
                foreach ($creatives as $creative) {
                    $data = [
                        'data' => [
                            'first_name' => $creative?->user?->first_name ?? '',
                            'category_name' => $creative?->category?->name ?? '',
                        ],
                        'receiver' => $creative?->user,
                    ];
                    SendEmailJob::dispatch($data, 'profile_completion_creative');
                    $creative->profile_completion_reminded_at = today();
                    $creative->save();
                    $creatives_processed += 1;
                }
            } catch (\Throwable $th) {
            }

            $this->info("Creatives processed: " . $creatives_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
