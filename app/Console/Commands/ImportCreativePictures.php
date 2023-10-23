<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class ImportCreativePictures extends Command
{
    protected $signature = 'import:creatives-profiles';

    protected $description = 'It imports only creative profile pictures';

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

        dump('Creative');
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

            if (isset($creativeData['post_meta']['_candidate_featured_image'][0])) {
                dump(sprintf('%d - User ID: %d Email: %s', $key, $user->id, $user->email));

                $featured_img = $creativeData['post_meta']['_candidate_featured_image'][0];

                $this->storeAttachment($featured_img, $user->id, 'profile_picture');
                // echo sprintf("<img src='%s'/>", $featured_img);
            }

            if (isset($creativeData['post_meta']['_candidate_cv_attachment'][0])) {
                dump(sprintf('%d - User ID: %d Email: %s', $key, $user->id, $user->email));
                $cvs = unserialize($creativeData['post_meta']['_candidate_cv_attachment'][0]);
                foreach ($cvs as $cv) {
                    $this->storeAttachment($cv, $user->id, 'resume');
                    // echo sprintf('%s', $cv);
                }

            }

            if ($endIndex > 0 && $key >= $endIndex) {
                break;
            }

        }
    }

    public function storeAttachment($url, $user_id, $resource_type)
    {
        $uuid = Str::uuid();

        $filename = basename($url);
        try {
            $contents = file_get_contents($url);
        } catch (\Exception $e) {
            dump($e->getMessage());

            return;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $folder = $resource_type.'/'.$uuid.'/'.$filename;
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
