<?php

namespace App\Console\Commands;

use App\Models\Creative;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShuffleFeaturedCreatives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shuffle:featured-creatives';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shuffles the sort order of a limited number of featured creatives for the homepage.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $count = settings('creative_count_homepage', 1);

        // Fetch all creatives that are currently featured.
        $featuredCreatives = Creative::where('is_featured', 1)
            ->whereHas('user', function ($query) {
                $query->where('is_visible', 1)
                    ->where('status', 1);
            })
            ->orderBy('featured_at', 'DESC')
            ->get();

        // Check if we have enough featured creatives to shuffle.
        if ($featuredCreatives->isEmpty()) {
            $this->info('No featured creatives found to shuffle.');
            return 0; // Exit successfully
        }

        // Randomly shuffle the collection of creatives.
        $creativesToUpdate = $featuredCreatives->take($count);
        $creativesToUpdate = $creativesToUpdate->shuffle();

        // Get the top 'N' creatives based on the 'creative_count_homepage' setting.

        $this->info("Shuffling and updating sort order for {$creativesToUpdate->count()} featured creatives.");

        // Start a database transaction for data integrity.
        DB::beginTransaction();
        try {
            // Loop through the limited, shuffled collection and update their sort_order.
            $order = 1;
            foreach ($creativesToUpdate as $creative) {
                $creative->sort_order = $order++;
                $creative->save();
            }

            // Update the sort_order for the remaining creatives to a value greater than the featured ones.
            // This ensures they are not shown on the homepage by default if the homepage only shows top N.
            $remainingCreatives = $featuredCreatives->diff($creativesToUpdate);
            foreach ($remainingCreatives as $creative) {
                // Ensure their sort order is a high value to push them to the end of the list.
                // You can also choose to reset it to 0 or null depending on your application logic.
                $creative->sort_order = $order++;
                $creative->save();
            }

            DB::commit();
            $this->info('Successfully shuffled and updated creative sort order.');

            // Clear the relevant cache so the changes are visible immediately on the website.
            Cache::forget('homepage_creatives');
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during shuffling: ' . $e->getMessage());
            return 1; // Exit with an error
        }

        return 0; // Exit successfully
    }
}
