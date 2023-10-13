<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Seo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(LocationSeeder::class);
        $this->call(YearsOfExperienceSeeder::class);

        Artisan::call('adagencycreatives:permission');

        \App\Models\User::factory(20)->create();

        User::where('id', '<', 10)->update(['role' => 3]); // 3:Agency
        User::where('id', '>', 15)->update(['role' => 2]); // 2:Advisor
        User::where('id', 1)->update([
            'email' => 'admin@gmail.com',
            'role' => 1,
            'status' => 1,
        ]); // 1:Admin

        User::where('id', 2)->update([
            'email' => 'agency@gmail.com',
            'role' => 3,
            'status' => 1,
        ]); // Agency

        User::where('id', 3)->update([
            'email' => 'creative@gmail.com',
            'role' => 4,
            'status' => 1,
        ]); // Agency

        // ********************************************************
        // ******************** AGENCY USERS **********************
        // ********************************************************

        $agency_users = User::where('role', 3)->get();

        $jobIds = [];
        $agency = Role::findByName('agency');
        foreach ($agency_users as $user) {
            $user->assignRole($agency);

            $data_user_id = ['user_id' => $user->id];

            \App\Models\Agency::factory()->create($data_user_id);

            \App\Models\Message::factory(50)->create(['sender_id' => $user->id]);

            $addresses = \App\Models\Address::factory(1)->create(array_merge($data_user_id, ['label' => 'business']));

            \App\Models\Phone::factory(3)->create($data_user_id);

            \App\Models\Link::factory(4)->create($data_user_id);

            // \App\Models\Attachment::factory(3)->create($data_user_id);

            $jobs = \App\Models\Job::factory(10)->create($data_user_id);

            foreach ($jobs as $job) {
                $jobIds[] = $job->id;
            }

        }

        // ********************************************************
        // ******************** CREATIVE USERS ********************
        // ********************************************************
        $creative = Role::findByName('creative');
        $creative_users = User::where('role', 4)->get();
        foreach ($creative_users as $user) {
            $user->assignRole($creative);

            $data_user_id = ['user_id' => $user->id];

            \App\Models\Creative::factory()->create($data_user_id);

            \App\Models\Address::factory(1)->create(array_merge($data_user_id, ['label' => 'personal']));

            \App\Models\Link::factory(3)->create($data_user_id);

            $attachments = \App\Models\Attachment::factory(3)->create($data_user_id);

            \App\Models\Education::factory(2)->create($data_user_id);
            \App\Models\Experience::factory(2)->create($data_user_id);

            for ($i = 0; $i < 3; $i++) {
                $attachment_id = $attachments->random()->id;
                $application = \App\Models\Application::factory()->create([
                    'user_id' => $user->id,
                    'job_id' => $jobIds[array_rand($jobIds)],
                    'attachment_id' => $attachment_id,
                ]
                );

                \App\Models\Note::factory(1)->create([
                    'user_id' => $user->id,
                    'application_id' => $application->id,
                ]
                );

                \App\Models\Bookmark::factory(1)->create([
                    'user_id' => $user->id,
                    'resource_id' => $jobIds[array_rand($jobIds)],
                ]
                );
            }
        }

        // *******************************************************
        // ******************** ADVISOR USERS ********************
        // *******************************************************
        $advisor = Role::findByName('advisor');
        $advisor_users = User::where('role', 2)->get();
        foreach ($advisor_users as $user) {
            $user->assignRole($advisor);

            \App\Models\Agency::factory()->create(
                [
                    'user_id' => $user->id,
                ]);
        }

        // ********************************************************
        // ******************** GENERAL SEEDERS *******************
        // ********************************************************
        $this->call(CategorySeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(MediaSeeder::class);
        $this->call(StrengthSeeder::class);

        $this->call(PlansTableSeeder::class);

        //Generate some more users
        // \App\Models\User::factory(15)->create();
        \App\Models\Order::factory(15)->create();
        \App\Models\Group::factory(3)->create();

        $this->call(PostSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(JobAlertSeeder::class);

        /**
         * Create Default SEO settings
         */
        settings([
            'site_name' => 'Ad Agency Creatives',
            'site_description' => 'Ad Agency Creatives is a community for advertising agency creatives to find jobs and mentorship, share resources, and gain access to the creative lounge.',
            'separator' => '|',

            'creative_title' => 'Site (%site_name%) %separator% %creatives_first_name% %creatives_last_name% %separator% Title (%creatives_title%) %separator% Location (%creatives_location%)',
            'creative_description' => '%creatives_about%  %separator% %site_name%',

            'job_title' => '%job_title% %separator% Location (%job_location%) %separator% %job_employment_type% %separator% Site (%site_name%)',
            'job_description' => '%job_description% %separator% %site_name%',

            'creative_spotlight_title' => 'Site Name (%site_name%) %separator% %post_name% %separator% %post_date%',
        ]);

        $this->call(PageSeeder::class);

        Artisan::call('import:users');
        Artisan::call('import:agencies');
        Artisan::call('import:creatives');
        Artisan::call('optimize:clear');
    }
}
