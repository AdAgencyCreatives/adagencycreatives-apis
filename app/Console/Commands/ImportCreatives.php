<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Creative;
use App\Models\Link;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCreatives extends Command
{
    protected $signature = 'import:creatives';

    protected $description = 'Import creatives from JSON file';

    public function handle()
    {
        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        foreach ($creativesData as $creativeData) {
            $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
            $authorEmail2 = $creativeData['author_email'];

            $user = User::where('email', $authorEmail1)->first();

            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {
                $agency = $this->createCreative($creativeData, $user);
                $agency->save();
            } else {
                dump('Creative not found', $authorEmail1);
            }

        }

        $this->info('Creatives data imported successfully.');
    }

    public function createCreative($data, $user)
    {
        $agency = new Creative();
        $agency->uuid = Str::uuid();
        $agency->user_id = $user->id;
        $agency->slug = Str::slug($data['post_title']);

        $agency->years_of_experience = $data['post_meta']['_candidate_experience_time'][0] ?? '';
        $agency->about = $data['post_content'];
        $agency->created_at = Carbon::createFromTimestamp($data['post_meta']['post_date'][0]);
        $agency->updated_at = now();

        if (isset($data['post_meta']['_candidate_job_title'][0])) {
            $title = $data['post_meta']['_candidate_job_title'][0];

            // Check if "-" is present in the title
            if (strpos($title, '-') !== false) {
                // Split the title by "-"
                $parts = explode('-', $title);

                // Take the first part (category name)
                $categoryName = trim($parts[0]);

                // Find the category by the extracted name
                $category = Category::where('name', 'LIKE', $categoryName)->first();

                if ($category) {
                    $agency->title = $category->name;
                    $agency->category_id = $category->id;
                }
            } else {
                // If no "-", use the title as is
                $category = Category::where('name', 'LIKE', $title)->first();

                if ($category) {
                    $agency->title = $category->name;
                    $agency->category_id = $category->id;
                }
            }

        }

        if (isset($data['post_meta']['_candidate_featured'][0]) && $data['post_meta']['_candidate_featured'][0] == 'on') {
            $agency->is_featured = true;
        }

        if ($data['post_meta']['_candidate_show_profile'][0] == 'hide') {
            $user->is_visible = false;
        }

        // Create LinkedIn link if provided
        if (isset($data['post_meta']['portfoliolink'][0]) && $data['post_meta']['portfoliolink'][0] !== '') {

            $this->createLink($user->id, 'portfolio', $data['post_meta']['portfoliolink'][0]);
        }

        // Create website link if provided
        if (isset($data['post_meta']['linkedinlink'][0]) && $data['post_meta']['linkedinlink'][0] !== '') {
            $this->createLink($user->id, 'linkedin', $data['post_meta']['linkedinlink'][0]);
        }

        if (isset($data['post_meta']['_employer_company_size'][0]) && $data['post_meta']['_employer_company_size'][0] !== '') {
            $agency->size = $data['post_meta']['_employer_company_size'][0];
        }

        if (isset($data['post_meta']['_candidate_phone'][0]) && $data['post_meta']['_candidate_phone'][0] !== '') {
            $this->createPhoneNumber($user->id, $data['post_meta']['_candidate_phone'][0]);
        }

        $user->save();

        return $agency;
    }

    public function createLink($userId, $label, $url)
    {
        Link::create([
            'uuid' => Str::uuid(),
            'user_id' => $userId,
            'label' => $label,
            'url' => $url,
        ]);
    }

    public function createPhoneNumber($userId, $phone_number)
    {
        Phone::create([
            'uuid' => Str::uuid(),
            'user_id' => $userId,
            'label' => 'personal',
            'country_code' => +1,
            'phone_number' => $phone_number,
        ]);
    }
}
