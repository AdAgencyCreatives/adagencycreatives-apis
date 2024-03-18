<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\PortfolioCaptureQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        DB::statement("INSERT INTO portfolio_capture_queue (user_id, url, capture, status, initiated_at, checked_at, created_at, updated_at) SELECT u.id, l.url, '', 0, null, null, now(), now() FROM users u INNER JOIN links l ON l.user_id = u.id WHERE u.role = 4 AND l.label = 'portfolio' AND u.id NOT IN (SELECT pcq.user_id FROM portfolio_capture_queue pcq);"); // Adds any user who is not already added to the queue
        DB::statement("UPDATE `portfolio_capture_queue` pcq SET pcq.status=0, pcq.initiated_at=NULL, pcq.checked_at=NULL WHERE pcq.capture IS NULL OR TRIM(pcq.capture) = ''"); // if capture url missing schedule to queue
        DB::statement("UPDATE `portfolio_capture_queue` pcq SET pcq.status=0, pcq.initiated_at=NULL, pcq.checked_at=NULL WHERE pcq.user_id IN (SELECT u.id FROM users u WHERE u.deleted_at IS NULL AND u.id NOT IN (SELECT att.user_id FROM `attachments` att WHERE att.`resource_type` LIKE 'website_preview_latest'));"); // if preview missing schedule to queue
        DB::statement("UPDATE `portfolio_capture_queue` pcq SET pcq.status=0, pcq.initiated_at=NULL, pcq.checked_at=NULL WHERE pcq.user_id IN (SELECT att.user_id FROM `attachments` att WHERE att.`resource_type` LIKE 'website_preview_latest' AND FLOOR(DATEDIFF(NOW(), att.updated_at)/7) > 6);"); // if 6 weeks passed after latest visual was captured, schedule to queue

        $item = PortfolioCaptureQueue::where('status', 0)
            ->orderBy('status')->orderBy('created_at')->orderBy('id')
            ->offset(0)->limit(1)->first();

        if ($item) {
            $data = [
                'capture' => '',
                'status' => 0,
                'initiated_at' => now(),
                'checked_at' => now(),
            ];

            $this->info(now() . " -- Processing: user_id:" . $item->user_id . ", url:[" . $item->url . "]");
            $result = $this->handleCustom($item->user_id, $item->url);

            if ($result) {
                $attachment = Attachment::where('user_id', $item->user_id)->where('resource_type', 'website_preview_latest')->first();
                $data['capture'] = getAttachmentBasePath() . $attachment->path;
                $data['status'] = 1;
                $data['checked_at'] = now();

                $item->update($data);

                $this->info($data['capture']);
            } else {
                $data['status'] = 2;
                $data['checked_at'] = now();
                $item->update($data);
            }
        } else {
            $this->info(now() . " -- Nothing to process, adding failed captures to queue again");
            DB::statement("UPDATE `portfolio_capture_queue` pcq SET pcq.status=0, pcq.initiated_at=NULL, pcq.checked_at=NULL WHERE pcq.status=2"); // if capture url missing schedule to queue
        }

        $this->info(now() . " -- Latest Visual Request Executed");
    }

    public function handleCustom($user_id, $url)
    {

        $url = ($url && filter_var($url, FILTER_VALIDATE_URL)) ? $url : 'http://' . $url;
        $api_url = sprintf("%s&url=%s%s", env('API_FLASH_BASE_URL'), $url, "&format=png&width=1366&height=768&fresh=true&quality=100&delay=10&no_cookie_banners=true&no_ads=true&no_tracking=true");

        try {
            $apiflashResponse = Http::timeout(60)->get($api_url);
            // Check if the request was successful
            if ($apiflashResponse->successful()) {
                // Store the image in AWS
                $this->storeAttachment($apiflashResponse, $user_id, 'website_preview_latest');

                $this->info(now() . ' -- Portfolio visuals latest generated successfully.');
                return true;
            } else {
                // Handle the case where the request to Apiflash failed
                $this->error(now() . ' -- Failed to generate portfolio visuals latest. Apiflash API request failed.');
                return false;
            }
        } catch (\Exception $e) {
            $this->error(now() . " -- Error:" . $e->getMessage());
        }
        return false;
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
            $existingAttachments = Attachment::withTrashed()->where('user_id', $user_id)->where('resource_type', $resource_type)->get();
            if ($existingAttachments) {
                foreach ($existingAttachments as $existingAttachment) {
                    $this->info(now() . " -- Deleting: " . $existingAttachment->path);
                    Storage::disk('s3')->delete($existingAttachment->path);
                    $existingAttachment->forceDelete();
                }
            }

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
