<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportCreativePortfolio extends Command
{
    protected $signature = 'import:creative-portfolio';

    protected $description = 'It imports only creative portfolio items';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $startIndex = $this->argument('startIndex');
        $endIndex = $this->argument('limit');

        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        dump('Portfolio Photos');
        foreach ($creativesData as $key => $creativeData) {

            if ($key < $startIndex) {
                continue;
            }

            $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
            $authorEmail2 = $creativeData['author_email'];

            $user = User::where('email', $authorEmail1)->first();
            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }
            if (! $user) {
                continue;
            }

            if (isset($creativeData['post_meta']['_candidate_portfolio_photos'][0])) {

                dump(sprintf('%d - User ID: %d Email: %s', $key, $user->id, $user->email));

                $portfolio_photos = unserialize($creativeData['post_meta']['_candidate_portfolio_photos'][0]);
                foreach ($portfolio_photos as $portfolio_photo) {

                    $this->storeAttachment($portfolio_photo, $user->id, 'portfolio_item');

                    echo sprintf("<img src='%s'/>", $portfolio_photo);
                }
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
