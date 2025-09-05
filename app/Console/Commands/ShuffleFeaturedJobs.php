<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShuffleFeaturedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shuffle:featured-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shuffles the sort order of a limited number of featured jobs for the homepage.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the number of jobs to feature from the settings.
        $count = settings('jobs_count_homepage',10);

        // Fetch all jobs that are currently featured and have a visible and active user.
        $featuredJobs = Job::where('is_featured', 1)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->get();

        // Check if there are any featured jobs to shuffle.
        if ($featuredJobs->isEmpty()) {
            $this->info('No featured jobs found to shuffle.');
            return 0; // Exit successfully
        }

        // Randomly shuffle the collection of jobs.
        $shuffledJobs = $featuredJobs->shuffle();

        // Take the top 'N' jobs from the shuffled collection based on the setting.
        $jobsToUpdate = $shuffledJobs->take($count);

        $this->info("Shuffling and updating sort order for {$jobsToUpdate->count()} featured jobs.");

        // Start a database transaction to ensure data integrity.
        DB::beginTransaction();
        try {
            // Loop through the limited, shuffled collection and update their sort_order.
            $order = 1;
            foreach ($jobsToUpdate as $job) {
                $job->sort_order = $order++;
                $job->save();
            }

            // For the remaining featured jobs, update their sort order to a higher value.
            // This ensures they are pushed to the end of the list and don't appear
            // on the homepage, which only displays the top 'N' jobs.
            $remainingJobs = $featuredJobs->diff($jobsToUpdate);
            foreach ($remainingJobs as $job) {
                $job->sort_order = $order++;
                $job->save();
            }

            DB::commit();
            $this->info('Successfully shuffled and updated job sort order.');

            // Clear the relevant cache to ensure the changes are immediately visible on the website.
            Cache::forget('homepage_jobs');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during shuffling: ' . $e->getMessage());
            return 1; // Exit with an error
        }

        return 0; // Exit successfully
    }
}
