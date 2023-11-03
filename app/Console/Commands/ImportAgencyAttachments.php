<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportAgencyAttachments extends Command
{
    protected $signature = 'import:agency-logos';

    protected $description = 'It imports only agency logos';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $startIndex = $this->argument('startIndex');
        $endIndex = $this->argument('limit');

        $jsonFilePath = public_path('export/agencies.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $agenciesData = json_decode($jsonContents, true);

        dump('Agency');
        foreach ($agenciesData as $key => $agencyData) {
            if ($key < $startIndex) {
                continue;
            }

            $authorEmail1 = $agencyData['post_meta']['_employer_email'][0];
            $authorEmail2 = $agencyData['author_email'];

            $user = User::where('email', $authorEmail1)->first();
            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if (isset($agencyData['post_meta']['_employer_featured_image'][0])) {
                dump(sprintf('%d - User ID: %d Email: %s', $key, $user->id, $user->email));

                $featured_img = $agencyData['post_meta']['_employer_featured_image'][0];

                $this->storeAttachment($featured_img, $user->id, 'agency_logo');
                echo sprintf("<img src='%s'/>", $featured_img);
                if ($endIndex > 0 && $key >= $endIndex) {
                    break;
                }
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