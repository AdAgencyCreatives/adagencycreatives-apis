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
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }
}
