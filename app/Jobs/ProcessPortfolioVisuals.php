<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ProcessPortfolioVisuals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;

    protected $url;

    public function __construct($user_id, $url)
    {
        $this->user_id = $user_id;
        $this->url = $url;
    }

    public function handle()
    {
        $url = $this->url;
        $user_id = $this->user_id;

        Artisan::call('portfolio:generate', ['user_id' => $user_id, 'url' => $url]);
    }
}
