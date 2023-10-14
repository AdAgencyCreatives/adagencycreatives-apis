<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Creative;
use App\Models\Industry;
use App\Models\Link;
use App\Models\Location;
use App\Models\Media;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportCreatives extends Command
{
    protected $signature = 'import:creatives';

    protected $description = 'Import creatives from JSON file';

    public function handle()
    {
        DB::table('creatives')->truncate();

        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        $industry_media_experiences = [];
        foreach ($creativesData as $creativeData) {
            $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
            $authorEmail2 = $creativeData['author_email'];
            // if($authorEmail1 != 'roye.segal@gmail.com') {
            //     continue;
            // }
            $user = User::where('email', $authorEmail1)->first();

            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {
                $this->createCreative($creativeData, $user, $industry_media_experiences);
                // $this->createLocation($creativeData, $user);
            } else {
                dump('Creative not found', $authorEmail1);
            }

        }

        $this->info('Creatives data imported successfully.');
    }

    public function createCreative($data, $user, $industry_media_experiences)
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
            $agency->title = $data['post_meta']['_candidate_job_title'][0];
        }

        if (isset($data['post_meta']['_candidate_featured'][0]) && $data['post_meta']['_candidate_featured'][0] == 'on') {
            $agency->is_featured = true;
        }

        $agency = $this->mapEmploymentType($data, $agency);
        $agency = $this->mapMediaExperience($data, $agency, $industry_media_experiences);
        $agency = $this->mapIndustryExperience($data, $agency, $industry_media_experiences);

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
        $agency->save();
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

    public function mapEmploymentType($data, $agency)
    {
        if (isset($data['post_meta']['custom-multiselect-31509246'][0])) {
            $employmentTypesArray = unserialize($data['post_meta']['custom-multiselect-31509246'][0]);

            // Replace "Contract" with "Contract 1099"
            $employmentTypesArray = array_map(function ($type) {
                return ($type === 'Contract') ? 'Contract 1099' : $type;
            }, $employmentTypesArray);

            $employmentTypesString = implode(',', $employmentTypesArray);
            $agency->employment_type = $employmentTypesString;
        }

        return $agency;
    }

    public function mapIndustryExperience($data, $agency, $industry_media_experiences)
    {
        if (isset($data['post_meta']['custom-multiselect-31108659'][0])) {
            $IndustryExperienceArray = unserialize($data['post_meta']['custom-multiselect-31108659'][0]);
            $industrUuids = [];
            foreach ($IndustryExperienceArray as $industryName) {
                // Check if the UUID is already in the dictionary
                if (isset($industry_media_experiences[$industryName])) {
                    $industrUuids[] = $industry_media_experiences[$industryName];
                } else {
                    $media = Media::where('name', $industryName)->first();
                    if (! $media) {
                        $media = Industry::where('name', $industryName)->first();
                    }
                    if ($media) {
                        $industrUuids[] = $media->uuid;
                        // Store the association in the dictionary
                        $industry_media_experiences[$industryName] = $media->uuid;
                    }
                }
            }
            $agency->industry_experience = implode(',', $industrUuids);
        }

        return $agency;
    }

    public function mapMediaExperience($data, $agency, $industry_media_experiences)
    {
        if (isset($data['post_meta']['media-experience'][0])) {
            $mediaExperienceArray = unserialize($data['post_meta']['media-experience'][0]);
            $mediaUuids = [];
            foreach ($mediaExperienceArray as $mediaName) {
                // Check if the UUID is already in the dictionary
                if (isset($industry_media_experiences[$mediaName])) {
                    $mediaUuids[] = $industry_media_experiences[$mediaName];
                } else {
                    $media = Media::where('name', $mediaName)->first();
                    if (! $media) {
                        $media = Industry::where('name', $mediaName)->first();
                    }
                    if ($media) {
                        $mediaUuids[] = $media->uuid;
                        // Store the association in the dictionary
                        $industry_media_experiences[$mediaName] = $media->uuid;
                    }
                }
            }
            $agency->media_experience = implode(',', $mediaUuids);
        }

        return $agency;
    }

    public function createLocation($data, $user)
    {
        $state = '';
        $city = '';
        foreach ($data['location'] as $location) {
            $locationModel = Location::where('slug', $location['slug'])->first();

            if ($location['parent'] == 0) {
                $state = $locationModel->id;
            } else {
                $city = $locationModel->id;
            }
        }

        $address = new Address();
        $address->uuid = Str::uuid();
        $address->user_id = $user->id;
        $address->label = 'personal';
        $address->country_id = 1;
        $address->state_id = $state;
        $address->city_id = $city;
        // $address->save();

    }
}
