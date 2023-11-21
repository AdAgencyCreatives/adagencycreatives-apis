<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Agency;
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

class ImportAgencies extends Command
{
    protected $signature = 'import:agencies';

    protected $description = 'Import agencies from JSON file';

    public function handle()
    {
        // DB::table('agencies')->truncate();

        $jsonFilePath = public_path('export/agencies.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $agenciesData = json_decode($jsonContents, true);

        $industry_media_experiences = [];
        foreach ($agenciesData as $agencyData) {
            $authorEmail1 = $agencyData['post_meta']['_employer_email'][0];
            $authorEmail2 = $agencyData['author_email'];

            // if($authorEmail1 != 'contacttpn@adagencycreatives.com') {
            //     continue;
            // }

            $user = User::where('email', $authorEmail1)->first();

            if (! $user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {

                $agency = $this->createAgency($agencyData, $user);
                $this->createLocation($agencyData, $user);
                $agency->industry_experience = $this->mapIndustryExperience($agencyData, $user, $industry_media_experiences);
                $agency->save();
            } else {
                dump('Agency not found', $authorEmail1);
                $user->username = sprintf('%s_dummy', $user->username);
                $user->save();
            }

        }

        $this->info('Agencies data imported successfully.');
    }

    public function createAgency($data, $user)
    {
        $agency = new Agency();
        $agency->uuid = Str::uuid();
        $agency->user_id = $user->id;
        $agency->name = $data['post_title'];
        $agency->slug = $user->username;
        $agency->about = $data['post_content'];
        $agency->created_at = Carbon::createFromTimestamp($data['post_meta']['post_date'][0]);
        $agency->updated_at = now();

        if ($data['post_meta']['_employer_show_profile'][0] == 'hide') {
            $user->is_visible = false;
        }

        // Create LinkedIn link if provided
        if (isset($data['post_meta']['linkedinlink'][0]) && $data['post_meta']['linkedinlink'][0] !== '') {

            $this->createLink($user->id, 'linkedin', $data['post_meta']['linkedinlink'][0]);
        }

        // Create website link if provided
        if (isset($data['post_meta']['_employer_website'][0]) && $data['post_meta']['_employer_website'][0] !== '') {
            $this->createLink($user->id, 'website', $data['post_meta']['_employer_website'][0]);
        }

        if (isset($data['post_meta']['_employer_company_size'][0]) && $data['post_meta']['_employer_company_size'][0] !== '') {
            $agency->size = $data['post_meta']['_employer_company_size'][0];
        }

        if (isset($data['post_meta']['_employer_phone'][0]) && $data['post_meta']['_employer_phone'][0] !== '') {
            $this->createPhoneNumber($user->id, $data['post_meta']['_employer_phone'][0]);
        }

        if (isset($data['post_meta']['_viewed_count'][0])) {
            $agency->views = $data['post_meta']['_viewed_count'][0];
        }

        $user->status = 'active';
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
            'label' => 'business',
            'country_code' => +1,
            'phone_number' => $phone_number,
        ]);
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
            $address->label = 'business';
            $address->country_id = 1;
            $address->state_id = $state ?? 1;
            $address->city_id = $city ?? $address->state_id + 1;
            $address->save();
        } catch (\Exception $e) {

        }

    }

    public function mapIndustryExperience($data, $creative, $industry_media_experiences)
    {
        $industrUuids = [];
        $count = 0;
        foreach ($data['categories'] as $industryName) {
            $industryName = $industryName['name'];

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

        return implode(',', $industrUuids);
    }
}
