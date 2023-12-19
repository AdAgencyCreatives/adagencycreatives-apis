<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Creative;
use App\Models\User;
use Illuminate\Console\Command;

class ImportCreativesCategories extends Command
{
    protected $signature = 'import:creatives-categories';

    protected $description = 'Import creatives categories from JSON file';

    public function handle()
    {
        // DB::table('creatives')->truncate();

        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        foreach ($creativesData as $creativeData) {
            $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
            $authorEmail2 = $creativeData['author_email'];

            // if($authorEmail1 != 'cassidyfletcherthewriter@gmail.com') {
            //     continue;
            // }

            // dd($creativeData);
            $user = User::where('email', $authorEmail1)->first();

            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {

                try {
                    $creative = Creative::where('user_id', $user->id)->first();

                    if ($creative) {
                        $category = Category::where('name', $creativeData['categories'])->first();
                        if ($category) {
                            $creative->category_id = $category->id;
                            $creative->save();
                        }
                    }

                } catch (\Exception $e) {
                    dump($creative);
                    dd($e->getMessage());
                }

            } else {
                dump('Creative not found', $authorEmail1);
            }
            dump($authorEmail1);
        }

        $this->info('Creatives categories imported successfully.');
    }
}
