<?php

namespace App\Console\Commands;

use App\Models\CreativeSpotlight;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportCreativeSpotlight extends Command
{
    protected $signature = 'import:creative-spotlights';

    protected $description = 'It imports only creative spotlights';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $startIndex = $this->argument('startIndex');
        $endIndex = $this->argument('limit');

        $jsonFilePath = public_path('export/spotlights.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $SpotlightsData = json_decode($jsonContents, true);

        dump('Spotlights');

        foreach ($SpotlightsData as $key => $Spotlight) {
            if ($key < $startIndex) {
                continue;
            }

            $post_title = $Spotlight['post_data']['post_title'];
            $pattern = '/,\s(.*?)\s(.*?)$/';
            if (preg_match($pattern, $post_title, $matches)) {
                $firstName = $matches[1];
                $lastName = $matches[2];
                try {
                    $user = User::where('first_name', $firstName)
                        ->where('last_name', $lastName)
                        ->where('status', 1)->first();

                } catch (\Exception $e) {
                    dump($firstName, $lastName);

                    continue;
                }

                if (isset($Spotlight['post_meta']['enclosure'][0])) {

                    $spotlight_url = $Spotlight['post_meta']['enclosure'][0];
                    $spotlight_title = $Spotlight['post_data']['post_title'];
                    $spotlight_slug = $Spotlight['post_data']['post_name'];
                    if (preg_match('/(.+\.mp4)\s/', $spotlight_url, $matches)) {
                        $partBeforeMp4 = $matches[1];
                        $this->storeAttachment($partBeforeMp4, 'creative_spotlight', $spotlight_title, $spotlight_slug);
                        dump(sprintf('%d - User ID: %d Email: %s', $key, $user->id, $user->email));
                        if ($endIndex > 0 && $key >= $endIndex) {
                            break;
                        }
                    }
                }

            } else {
                dump($post_title, 'not found');
            }
        }

    }

    public function storeAttachment($url, $resource_type, $title, $slug)
    {
        try {
            $response = Http::get($url);

            dump("Downloading $url");
            if ($response->successful()) {
                $uuid = Str::uuid();
                $filename = basename($url);

                $folder = $resource_type.'/'.$uuid.'/'.$filename;
                $filePath = Storage::disk('s3')->put($folder, $response->body());

                $attachment = CreativeSpotlight::create([
                    'uuid' => $uuid,
                    'title' => $title,
                    'path' => $folder,
                    'name' => $filename,
                    'slug' => $slug,
                    'status' => 'approved',
                ]);

                return $attachment;
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
