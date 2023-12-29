<?php

namespace App\Console\Commands;


use App\Models\Attachment;
use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class GenerateMentorResourceVisual extends Command
{
    protected $signature = 'mentor-resource:generate';

    protected $description = 'Generate mentor resource visuals';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('id', InputArgument::OPTIONAL, 'Description of user_id argument');
        $this->addArgument('url', InputArgument::OPTIONAL, 'Description of url argument');
        $this->addArgument('resource_type', InputArgument::OPTIONAL, 'Description of url argument');
    }

    public function handle()
    {
        $id = $this->argument('id');
        $url = $this->argument('url');
        $resource_type = $this->argument('resource_type');

        $url = ($url && filter_var($url, FILTER_VALIDATE_URL)) ? $url : 'http://' . $url;

        $api_url = sprintf("%s&url=%s%s", env('API_FLASH_BASE_URL'), $url, "&format=png&width=1366&height=768&fresh=true&quality=100&delay=10&no_cookie_banners=true&no_ads=true&no_tracking=true") ;


        $apiflashResponse = Http::get($api_url);
        // Check if the request was successful
        if ($apiflashResponse->successful()) {
            // Store the image in AWS
            $this->storeAttachment($apiflashResponse, $id, $resource_type);

            $this->info('Portfolio visuals generated successfully.');
        } else {
            // Handle the case where the request to Apiflash failed
            $this->error('Failed to generate portfolio visuals. Apiflash API request failed.');
        }

        $this->info('Portfolio visuals generated successfully.');
    }

    public function storeAttachment($imageData, $id, $resource_type)
    {
        try {
            $uuid = Str::uuid();
            $filename = sprintf('%s_%s', $id,$resource_type);

            // $img = file_get_contents($imageData);
            $folder = $resource_type . '/' . $uuid . '/' . $filename;
            $filePath = Storage::disk('s3')->put($folder, $imageData);

            $attachment = Attachment::create([
                'uuid' => $uuid,
                'user_id' => $id,
                'resource_type' => $resource_type,
                'path' => $folder,
                'name' => $filename,
                'extension' => 'png',
            ]);

            $resource = Resource::where('id', $id)->first();
            $resource->update([
                'preview_link' => $folder
            ]);

            return $attachment;

        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}

//
