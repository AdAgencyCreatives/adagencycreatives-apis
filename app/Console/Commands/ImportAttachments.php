<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportAttachments extends Command
{
    protected $signature = 'import:attachments';

    protected $description = 'It will fetch all the attachments (profile_picture, agency logo etc) and stores in aws';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $startIndex = $this->argument('startIndex');
        $limit = $this->argument('limit');

        $jsonFilePath = public_path('export/agencies.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $agenciesData = json_decode($jsonContents, true);

        $count = 1;
        dump('Agency');
        // foreach ($agenciesData as $agencyData) {
        //     if ($count < $startIndex) {
        //         $count++;

        //         continue; // Skip this image if already processed
        //     }

        //     $authorEmail1 = $agencyData['post_meta']['_employer_email'][0];
        //     $authorEmail2 = $agencyData['author_email'];

        //     $user = User::where('email', $authorEmail1)->first();
        //     if (! $user) {
        //         $user = User::where('email', $authorEmail2)->first();
        //     }
        //     if (! $user) {
        //         continue;
        //     }

        //     dump(sprintf('User ID: %d', $user->id));

        //     if (isset($agencyData['post_meta']['_employer_featured_image'][0])) {
        //         $featured_img = $agencyData['post_meta']['_employer_featured_image'][0];

        //         $this->storeAttachment($featured_img, $user->id, 'agency_logo');
        //         echo sprintf("<img src='%s'/>", $featured_img);
        //         echo "</br>" . $count;
        //         $count++;
        //     }

        // }

        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        dump('Creative');
        // foreach ($creativesData as $creativeData) {
        //     if ($count < $startIndex) {
        //         $count++;

        //         continue; // Skip this image if already processed
        //     }

        //     $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
        //     $authorEmail2 = $creativeData['author_email'];

        //     $user = User::where('email', $authorEmail1)->first();
        //     if (! $user) {
        //         $user = User::where('email', $authorEmail2)->first();
        //     }
        //     if (! $user) {
        //         continue;
        //     }

        //     dump(sprintf('User ID: %d', $user->id));
        //     if (isset($creativeData['post_meta']['_candidate_featured_image'][0])) {
        //         $featured_img = $creativeData['post_meta']['_candidate_featured_image'][0];

        //         $this->storeAttachment($featured_img, $user->id, 'profile_picture');
        //         echo sprintf("<img src='%s'/>", $featured_img);
        //         echo "</br>" . $count;
        //         $count++;
        //     }

        //     if (isset($creativeData['post_meta']['_candidate_cv_attachment'][0])) {
        //         $cvs = unserialize($creativeData['post_meta']['_candidate_cv_attachment'][0]);
        //         foreach ($cvs as $cv) {
        //             $this->storeAttachment($cv, $user->id, 'resume');
        //             echo sprintf('%s', $cv);
        //         }
        //         echo "</br>" . $count;
        //         $count++;
        //     }

        //     if (isset($creativeData['post_meta']['_candidate_portfolio_photos'][0])) {

        //         $portfolio_photos = unserialize($creativeData['post_meta']['_candidate_portfolio_photos'][0]);
        //         foreach ($portfolio_photos as $portfolio_photo) {

        //             $this->storeAttachment($portfolio_photo, $user->id, 'portfolio_item');
        //             echo sprintf("<img src='%s'/>", $portfolio_photo);
        //         }
        //         echo "</br>" . $count;
        //         $count++;
        //     }

        // }

        $jsonFilePath = public_path('export/spotlights.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $SpotlightsData = json_decode($jsonContents, true);

        dump('Spotlights');

        foreach ($SpotlightsData as $Spotlight) {
            if ($count < $startIndex) {
                $count++;
                continue; // Skip this image if already processed
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

                } catch(\Exception $e) {
                    dump($firstName, $lastName);
                    continue;
                }


                dump(sprintf('User ID: %d', $user->id));
                if (isset($Spotlight['post_meta']['enclosure'][0])) {
                    $spotlight_url = $Spotlight['post_meta']['enclosure'][0];
                    if (preg_match('/(.+\.mp4)\s/', $spotlight_url, $matches)) {
                        $partBeforeMp4 = $matches[1];
                        $this->storeAttachment($partBeforeMp4, $user->id, 'creative_spotlight');
                        echo sprintf("%s", $partBeforeMp4);
                        echo "</br>" . $count;
                        $count++;
                    }
                }

            } else {
                dump($post_title, "not found");
            }
        }

    }

    public function storeAttachment($url, $user_id, $resource_type)
    {
        $uuid = Str::uuid();

        $filename = basename($url);
        $contents = file_get_contents($url);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $folder = $resource_type . '/' . $uuid . '/' . $filename;
        $filePath = Storage::disk('s3')->put($folder, $contents);

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
}