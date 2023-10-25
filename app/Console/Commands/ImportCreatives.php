<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Creative;
use App\Models\Education;
use App\Models\Experience;
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
        // DB::table('creatives')->truncate();

        $jsonFilePath = public_path('export/creatives.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $creativesData = json_decode($jsonContents, true);

        $industry_media_experiences = [];
        foreach ($creativesData as $creativeData) {
            $authorEmail1 = $creativeData['post_meta']['_candidate_email'][0];
            $authorEmail2 = $creativeData['author_email'];
            // if($authorEmail1 != 'hmbellipro@gmail.com') {
            //     continue;
            // }
            $user = User::where('email', $authorEmail1)->first();

            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {
                $this->createCreative($creativeData, $user, $industry_media_experiences);
                $this->createLocation($creativeData, $user);
                $this->storeEducation($creativeData, $user);
                $this->storeExperience($creativeData, $user);
            } else {
                dump('Creative not found', $authorEmail1);
            }

        }

        $this->info('Creatives data imported successfully.');
    }

    public function createCreative($data, $user, $industry_media_experiences)
    {
        $creative = new Creative();
        $creative->uuid = Str::uuid();
        $creative->user_id = $user->id;
        $creative->slug = Str::slug($data['post_title']);

        $creative->years_of_experience = $data['post_meta']['_candidate_experience_time'][0] ?? '';
        $creative->about = $data['post_content'];
        $creative->created_at = Carbon::createFromTimestamp($data['post_meta']['post_date'][0]);
        $creative->updated_at = now();

        if (isset($data['post_meta']['_candidate_job_title'][0])) {
            $creative->title = $data['post_meta']['_candidate_job_title'][0];
        }

        if (isset($data['post_meta']['_candidate_featured'][0]) && $data['post_meta']['_candidate_featured'][0] == 'on') {
            $creative->is_featured = true;
        }

        if (isset($data['post_meta']['custom-radio-28265865'][0]) && $data['post_meta']['custom-radio-28265865'][0] == 'Yes') {
            $creative->is_opentorelocation = true;
        }

        $creative = $this->mapEmploymentType($data, $creative);
        $creative = $this->mapMediaExperience($data, $creative, $industry_media_experiences);
        $creative = $this->mapIndustryExperience($data, $creative, $industry_media_experiences);

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
            $creative->size = $data['post_meta']['_employer_company_size'][0];
        }

        if (isset($data['post_meta']['_candidate_phone'][0]) && $data['post_meta']['_candidate_phone'][0] !== '') {
            $this->createPhoneNumber($user->id, $data['post_meta']['_candidate_phone'][0]);
        }

        if (isset($data['post_meta']['_viewed_count'][0])) {
            $creative->views = $data['post_meta']['_viewed_count'][0];
        }

        $user->save();
        $creative->save();
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

    public function mapEmploymentType($data, $creative)
    {
        if (isset($data['post_meta']['custom-multiselect-31509246'][0])) {
            $employmentTypesArray = unserialize($data['post_meta']['custom-multiselect-31509246'][0]);

            $employmentTypesString = implode(',', $employmentTypesArray);
            $creative->employment_type = $employmentTypesString;
        }

        return $creative;
    }

    public function mapIndustryExperience($data, $creative, $industry_media_experiences)
    {
        if (isset($data['post_meta']['custom-multiselect-31108659'][0])) {
            $IndustryExperienceArray = unserialize($data['post_meta']['custom-multiselect-31108659'][0]);
            $industrUuids = [];
            $count = 0;
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
                $count++;
                if ($count > 10) {
                    break;
                }
            }
            $creative->industry_experience = implode(',', $industrUuids);
        }

        return $creative;
    }

    public function mapMediaExperience($data, $creative, $industry_media_experiences)
    {
        if (isset($data['post_meta']['media-experience'][0])) {
            $mediaExperienceArray = unserialize($data['post_meta']['media-experience'][0]);
            $mediaUuids = [];
            $count = 0;
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
                $count++;
                if ($count > 10) {
                    break;
                }
            }
            $creative->media_experience = implode(',', $mediaUuids);
        }

        return $creative;
    }

    public function createLocation($data, $user)
    {
        try {
            $state = null;
            $city = null;
            foreach ($data['location'] as $location) {
                $locationModel = Location::where('name', $location['name'])->first();
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
            $address->state_id = $state ?? 1;
            $address->city_id = $city ?? $address->state_id + 1;
            $address->save();
        } catch (\Exception $e) {

        }

    }

    public function storeEducation($data, $creative)
    {
        if (isset($data['post_meta']['_candidate_education'][0])) {
            $educationArray = unserialize($data['post_meta']['_candidate_education'][0]);
            foreach ($educationArray as $edu) {
                $education = new Education();
                $education->uuid = Str::uuid();
                $education->user_id = $creative->id;
                $education->degree = $edu['title'] ?? '';
                $education->college = $edu['academy'] ?? '';
                $education->save();
            }
        }
    }

    public function storeExperience($data, $creative)
    {
        if (isset($data['post_meta']['_candidate_experience'][0])) {
            $experienceArray = unserialize($data['post_meta']['_candidate_experience'][0]);
            foreach ($experienceArray as $edu) {

                $experience = new Experience();
                $experience->uuid = Str::uuid();
                $experience->user_id = $creative->id;
                $experience->title = $edu['title'] ?? '';
                $experience->company = $edu['company'] ?? '';
                $experience->description = $edu['description'] ?? '';
                $experience->save();
            }
        }
    }
}