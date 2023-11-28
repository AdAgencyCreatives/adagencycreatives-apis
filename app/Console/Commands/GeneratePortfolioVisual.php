<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class GeneratePortfolioVisual extends Command
{
    protected $signature = 'portfolio:generate';

    protected $description = 'Generate portfolio visuals';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('user_id', InputArgument::OPTIONAL, 'Description of user_id argument');
        $this->addArgument('url', InputArgument::OPTIONAL, 'Description of url argument');
    }

    public function handle()
    {
        $user_id = $this->argument('user_id');
        $url = $this->argument('url');

        $url = ($url && filter_var($url, FILTER_VALIDATE_URL)) ? $url : 'http://'.$url;

        $googlePagespeedResponse = Http::get('https://www.googleapis.com/pagespeedonline/v5/runPagespeed', [
            'screenshot' => 'true',
            'url' => $url,
        ]);

        $googlePagespeedObject = $googlePagespeedResponse->json();

        if (isset($googlePagespeedObject['lighthouseResult']['audits']['final-screenshot']['details']['data'])) {
            $screenshot = $googlePagespeedObject['lighthouseResult']['audits']['final-screenshot']['details']['data'];
            $screenshot = str_replace(['_', '-'], ['/', '+'], $screenshot);

            $this->storeAttachment($screenshot, $user_id, 'website_preview');
        }

        $this->info('Portfolio visuals generated successfully.');
    }

    public function storeAttachment($imageData, $user_id, $resource_type)
    {
        try {
            $uuid = Str::uuid();
            $filename = sprintf('%s_portfolio', $user_id);

            $img = file_get_contents($imageData);
            $folder = $resource_type.'/'.$uuid.'/'.$filename;
            $filePath = Storage::disk('s3')->put($folder, $img);

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
