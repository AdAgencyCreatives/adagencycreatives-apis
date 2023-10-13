<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Link;
use App\Models\Phone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportAgencies extends Command
{
    protected $signature = 'import:agencies';

    protected $description = 'Import agencies from JSON file';

    public function handle()
    {
        $jsonFilePath = public_path('export/agencies.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $agenciesData = json_decode($jsonContents, true);

        foreach ($agenciesData as $agencyData) {
            $authorEmail1 = $agencyData['post_meta']['_employer_email'][0];
            $authorEmail2 = $agencyData['author_email'];
            $user = User::where('email', $authorEmail1)->first();

            if (!$user) {
                $user = User::where('email', $authorEmail2)->first();
            }

            if ($user) {
                $agency = $this->createAgency($agencyData, $user);
                $agency->save();
            } else {
                dump('Agency not found', $authorEmail1);
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
        $agency->slug = Str::slug($data['post_title']);
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
}