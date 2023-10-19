<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Location;
use App\Models\Media;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportJobs extends Command
{
    protected $signature = 'import:jobs';

    protected $description = 'Import jobs from JSON file';

    public function handle()
    {
        // DB::table('job_posts')->truncate();

        $jsonFilePath = public_path('export/jobs.json');
        $jsonContents = file_get_contents($jsonFilePath);
        $jobsData = json_decode($jsonContents, true);

        $industry_media_experiences = [];
        foreach ($jobsData as $jobdata) {
            $authorEmail1 = $jobdata['author_email'];
            // if($authorEmail1 != 'contactburrell@adagencycreatives.com') {
            //     continue;
            // }
            $user = User::where('email', $authorEmail1)->first();

            if ($user) {
                $this->createJob($jobdata, $user, $industry_media_experiences);

            } else {
                dump('Job not found', $authorEmail1);
            }

        }

        $this->info('Jobs data imported successfully.');
    }

    public function createJob($data, $user, $industry_media_experiences)
    {
        $job = new Job();
        $job->uuid = Str::uuid();
        $job->user_id = $user->id;

        $job->title = $data['post_title'];
        $job->slug = $data['post_data']['post_name'] ?? null;
        $job->description = $data['post_content'];
        $job->employment_type = $data['job_type'];
        $job->years_of_experience = $data['post_meta']['_job_experience'][0] ?? '';
        $job->status = $this->mapStatus($data['post_data']['post_status']);
        $job->created_at = $data['post_data']['post_date'];
        $job->updated_at = now();

        if (isset($data['post_meta']['_job_apply_type'][0])) {
            $job->apply_type = $data['post_meta']['_job_apply_type'][0];
        }

        if (isset($data['post_meta']['_job_salary'][0])) {
            $job->salary_range = $data['post_meta']['_job_salary'][0];
        }

        if (isset($data['post_meta']['_job_apply_url'][0])) {
            $job->external_link = $data['post_meta']['_job_apply_url'][0];
        }

        if (isset($data['post_meta']['_job_expiry_date'][0])) {
            $job->expired_at = $data['post_meta']['_job_expiry_date'][0];
        }

        if (isset($data['post_meta']['_job_featured'][0]) && $data['post_meta']['_job_featured'][0] == 'on') {
            $job->is_featured = true;
        }

        if (isset($data['post_meta']['custom-radio-24464235'][0]) && $data['post_meta']['custom-radio-24464235'][0] == 'Yes') {
            $job->is_remote = true;
        }

        if (isset($data['post_meta']['custom-radio-19364131'][0]) && $data['post_meta']['custom-radio-19364131'][0] == 'Yes') {
            $job->is_hybrid = true;
        }

        $new_location = $this->createLocation($data, $job);
        if (! empty($new_location)) {
            if (isset($new_location['state'])) {
                $job->state_id = $new_location['state'];
            }
            if (isset($new_location['city'])) {
                $job->city_id = $new_location['city'];
            }
        }
        $job = $this->mapCategory($data, $job, $industry_media_experiences);
        $job = $this->mapMediaExperience($data, $job, $industry_media_experiences);
        $job = $this->mapIndustryExperience($data, $job, $industry_media_experiences);

        if (isset($data['post_meta']['_viewed_count'][0])) {
            $job->views = $data['post_meta']['_viewed_count'][0];
        }
        $job->save();
    }

    public function mapStatus($status)
    {
        if ($status == 'publish') {
            $status = 'approved';
        }

        return $status;

    }

    public function mapCategory($data, $job)
    {
        if ($data['category'] != '') {
            $category = Category::where('name', $data['category'])->first();
            if ($category) {
                $job->category_id = $category->id;
            }
        }

        return $job;
    }

    public function mapIndustryExperience($data, $job, $industry_media_experiences)
    {
        if (isset($data['post_meta']['custom-multiselect-23162076'][0])) {
            $IndustryExperienceArray = unserialize($data['post_meta']['custom-multiselect-23162076'][0]);
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
            $job->industry_experience = implode(',', $industrUuids);
        }

        return $job;
    }

    public function mapMediaExperience($data, $job, $industry_media_experiences)
    {
        if (isset($data['post_meta']['custom-multiselect-30336124'][0])) {
            $mediaExperienceArray = unserialize($data['post_meta']['custom-multiselect-30336124'][0]);
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
            $job->media_experience = implode(',', $mediaUuids);
        }

        return $job;
    }

    public function createLocation($data)
    {
        try {
            $new_location = [];

            foreach ($data['location'] as $location) {

                $locationModel = Location::where('name', $location['name'])->first();

                if ($location['parent'] == 0) {
                    $new_location['state'] = $locationModel->id;
                } else {
                    $new_location['city'] = $locationModel->id;
                }
            }

            return $new_location;

        } catch (\Exception $e) {
            return [];
        }

    }
}