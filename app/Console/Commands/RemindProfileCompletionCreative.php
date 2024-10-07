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

            $users = User::where('role', '=', 4)->whereNull('profile_completed_at')
                ->whereNull('profile_completion_reminded_at')
                ->orderBy('created_at')
                ->take(30)
                ->get();

            $users_to_process = count($users);
            $users_processed = 0;

            $this->info("Creatives to process: " . $users_to_process);

            try {
                foreach ($users as $user) {
                    $data = [
                        'data' => [
                            'first_name' => $user?->first_name ?? '',
                            'category_name' => $user?->creative?->category?->name ?? '',
                        ],
                        'receiver' => $user,
                    ];
                    SendEmailJob::dispatch($data, 'profile_completion_creative');
                    $user->profile_completion_reminded_at = today();
                    $user->save();
                    $users_processed += 1;
                }
            } catch (\Throwable $th) {
            }

            $this->info("Creatives processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
