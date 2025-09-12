<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\MonthlyQuota;

class ResetMonthlyQuotas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quota:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets the monthly job quota for annual subscriptions.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting monthly quota reset...');

        // Find all subscriptions for the annual plan
        $subscriptions = Subscription::where('name', 'annual-monthly-quota')->get();

        foreach ($subscriptions as $subscription) {
            // Reset the monthly quota for each subscription
            $subscription->monthlyQuota()->update([
                'jobs_posted_this_month' => 0,
                'last_reset_at' => now(),
            ]);
        }

        $this->info('Monthly quotas have been reset successfully.');
        return Command::SUCCESS;
    }
}
