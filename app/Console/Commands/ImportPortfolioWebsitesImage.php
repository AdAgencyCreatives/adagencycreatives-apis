<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportPortfolioWebsitesImage extends Command
{
    protected $signature = 'import:creative-portfolio-websites-image';

    protected $description = 'It imports creatives portfolio websites previews';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $startIndex = $this->argument('startIndex');
        $endIndex = $this->argument('limit');

        $jsonFilePath = public_path('export/users.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $usersData = json_decode($jsonContents, true);

        dump('Website Portfolio');

        $count = 1;
        foreach ($usersData as $key => $user) {
            if ($key < $startIndex) {
                continue;
            }

            if(isset($user['user_meta']['candidate_id'][0])){
                $candidate_id = $user['user_meta']['candidate_id'][0];
                dump( sprintf("%d Email: %s candidate_id: %s", $count, $user['user_email'], $candidate_id ) );
                $count++;
                $url = sprintf("https://adagencycreatives.com/wp-content/themes/superio/images/portfolio/%s.png", $candidate_id);
                $laravel_user = User::where('email', $user['user_email'])->first();
                $this->storeAttachment($url, $laravel_user->id, 'website_preview');
            }

            if ($endIndex > 0 && $key >= $endIndex) {
                break;
            }



        }

    }

    public function storeAttachment($url, $user_id, $resource_type)
    {
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $uuid = Str::uuid();
                $filename = basename($url);
                // echo sprintf("<img src='%s'/>", $url);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $folder = $resource_type.'/'.$uuid.'/'.$filename;
                $filePath = Storage::disk('s3')->put($folder, $response->body());

                $attachment = Attachment::create([
                    'uuid' => $uuid,
                    'user_id' => $user_id,
                    'resource_type' => $resource_type,
                    'path' => $folder,
                    'name' => $filename,
                    'extension' => $extension,
                ]);

                return $attachment;
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
