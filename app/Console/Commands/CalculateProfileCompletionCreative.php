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
                    $this->info("Profile Completed: " . $progress . "% for Creative: " . $user?->full_name . ", " . $user?->email);
                } catch (\Exception $e2) {
                    $this->info("Calculate Profile Completion Failed for Creative: " . $user?->full_name);
                }
            }

            $this->info("Creatives processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }

    private function getCreativeProfileProgress($creative)
    {
        $progress = 0;
        $required_fields = 17;
        $completed_fields = 0;

        $completed_fields +=  strlen($creative?->user?->profile_picture?->path ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->portfolio_website_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->creative_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($creative?->user?->personal_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($creative?->title ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->category?->name ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->years_of_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->media_experience ?? "") > 0) ? 1 : 0;

        $address = $creative?->user?->addresses ? collect($creative?->user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            $completed_fields += (strlen($address?->state?->name  ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields += (strlen($creative?->strengths ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->employment_type ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($creative?->about ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }
}
