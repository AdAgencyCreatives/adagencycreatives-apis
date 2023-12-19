<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPortfolioVisuals;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;

class GeneratePortfolioImagesForMissingCreatives extends Command
{
    protected $signature = 'generate:missing-portfolios';
    protected $description = 'It generates portfolio previews for missing creatives';

    public function handle()
    {
        $creatives = User::where('role', 4)->get();
        foreach($creatives as $user) {
            $portfolio_website = $user->portfolio_website_link()->first();
            if ($portfolio_website) {
                $existing_preview = Attachment::where('user_id', $user->id)->where('resource_type', 'website_preview')->first();
                if(!$existing_preview){
                    ProcessPortfolioVisuals::dispatch($user->id, $portfolio_website->url);
                }

            }
        }
    }
}
