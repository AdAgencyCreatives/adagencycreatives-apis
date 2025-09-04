<?php

namespace App\Console\Commands;

use App\Models\Agency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShuffleFeaturedAgencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shuffle:featured-agencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shuffles the sort order of a limited number of featured agencies for the homepage.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch the number of agencies to feature from your settings.
        // You might need to adjust the key 'agency_count_homepage' to match your settings.
        $count = settings('agency_count_homepage', 10);

        // Fetch all agencies that are currently featured and active.
        $featuredAgencies = Agency::where('is_featured', 1)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->get();

        if ($featuredAgencies->isEmpty()) {
            $this->info('No featured agencies found to shuffle.');
            return 0;
        }

        // Randomly shuffle the collection of agencies.
        $shuffledAgencies = $featuredAgencies->shuffle();

        // Get the top 'N' agencies based on the 'agency_count_homepage' setting.
        $agenciesToUpdate = $shuffledAgencies->take($count);

        $this->info("Shuffling and updating sort order for {$agenciesToUpdate->count()} featured agencies.");

        // Start a database transaction for data integrity.
        DB::beginTransaction();
        try {
            // Set the sort_order for the selected agencies.
            $order = 1;
            foreach ($agenciesToUpdate as $agency) {
                $agency->sort_order = $order++;
                $agency->save();
            }

            // Set a high sort_order for the remaining agencies to push them to the end of the list.
            $remainingAgencies = $featuredAgencies->diff($agenciesToUpdate);
            foreach ($remainingAgencies as $agency) {
                $agency->sort_order = $order++;
                $agency->save();
            }

            DB::commit();
            $this->info('Successfully shuffled and updated agency sort order.');

            // Clear the relevant cache.
            Cache::forget('homepage_agencies');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during shuffling: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
