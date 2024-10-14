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
                    $this->info("Profile Completed: " . $progress . "% for Agency: " . $user?->full_name . ", " . $user?->email);
                } catch (\Exception $e2) {
                    $this->info("Calculate Profile Completion Failed for Agency: " . $user?->full_name);
                }
            }

            $this->info("Agencies processed: " . $users_processed);
        } catch (\Exception $e) {
            $this->info($e->getMessage());
        }
    }

    private function getAgencyProfileProgress($agency): int
    {
        $progress = 0;
        $required_fields = 16;
        $completed_fields = 0;

        $completed_fields +=  strlen($agency?->user?->agency_logo?->path ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->name ?? "") > 0) ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->agency_website_link?->url ?? '') > 0 ? 1 : 0;

        $address = $agency?->user?->addresses ? collect($agency?->user->addresses)->firstWhere('label', 'business') : null;
        if ($address) {
            $completed_fields += (strlen($address?->state?->name  ?? "") > 0) ? 1 : 0;
            $completed_fields += (strlen($address?->city?->name ?? "") > 0) ? 1 : 0;
        }

        $completed_fields +=  strlen($agency?->user?->agency_linkedin_link?->url ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->email ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->first_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->last_name ?? '') > 0 ? 1 : 0;
        $completed_fields +=  strlen($agency?->user?->business_phone?->phone_number ?? '') > 0 ? 1 : 0;
        $completed_fields += (strlen($agency?->about ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->industry_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->media_experience ?? "") > 0) ? 1 : 0;
        $completed_fields += ($agency?->is_remote || $agency?->is_hybrid || $agency?->is_onsite) ? 1 : 0;
        $completed_fields += (strlen($agency?->size ?? "") > 0) ? 1 : 0;
        $completed_fields += (strlen($agency?->slug ?? "") > 0) ? 1 : 0;

        $progress = intval(100 * $completed_fields / $required_fields);

        return $progress;
    }
}
