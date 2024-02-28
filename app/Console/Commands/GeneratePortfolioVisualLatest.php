<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class GeneratePortfolioVisualLatest extends Command
{
    protected $signature = 'portfolio_latest:generate';

    protected $description = 'Generate portfolio visuals latest';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        // $this->addArgument('user_id', InputArgument::OPTIONAL, 'Description of user_id argument');
        // $this->addArgument('url', InputArgument::OPTIONAL, 'Description of url argument');
    }

    public function handle()
    {

        $user_id = $this->argument('user_id');
        $url = $this->argument('url');

        $url = ($url && filter_var($url, FILTER_VALIDATE_URL)) ? $url : 'http://' . $url;

        $api_url = sprintf("%s&url=%s%s", env('API_FLASH_BASE_URL'), $url, "&format=png&width=1366&height=768&fresh=true&quality=100&delay=10&no_cookie_banners=true&no_ads=true&no_tracking=true") ;


        $apiflashResponse = Http::timeout(60)->get($api_url);
        // Check if the request was successful
        if ($apiflashResponse->successful()) {
            // Store the image in AWS
            $this->storeAttachment($apiflashResponse, $user_id, 'website_preview_latest');

            $this->info('Portfolio visuals latest generated successfully.');
        } else {
            // Handle the case where the request to Apiflash failed
            $this->error('Failed to generate portfolio visuals latest. Apiflash API request failed.');
        }

        $this->info('Portfolio visuals latest generated successfully.');
    }

    public function storeAttachment($imageData, $user_id, $resource_type)
    {
        try {
            $uuid = Str::uuid();
            $filename = sprintf('%s_portfolio', $user_id);

            // $img = file_get_contents($imageData);
            $folder = $resource_type . '/' . $uuid . '/' . $filename;
            $filePath = Storage::disk('s3')->put($folder, $imageData);

            //Delete previous preview
            Attachment::where('user_id', $user_id)->where('resource_type', $resource_type)->delete();

            $attachment = Attachment::create([
                'uuid' => $uuid,
                'user_id' => $user_id,
                'resource_type' => $resource_type,
                'path' => $folder,
                'name' => $filename,
                'extension' => 'png',
            ]);

            return $attachment;

        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
