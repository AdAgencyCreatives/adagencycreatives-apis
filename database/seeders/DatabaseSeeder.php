<?php

namespace Database\Seeders;

use App\Models\Creative;
use App\Models\Seo;
use App\Models\User;
use GuzzleHttp\Promise\Create;
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
        // ********************************************************
        // ******************** GENERAL SEEDERS *******************
        // ********************************************************
        $this->call(CategorySeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(MediaSeeder::class);
        $this->call(StrengthSeeder::class);
        $this->call(PlansTableSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(YearsOfExperienceSeeder::class);
        $this->call(PageSeeder::class);
        Artisan::call('adagencycreatives:permission');

        \App\Models\User::factory(5)->create();

        User::where('id', 1)->update([
            'email' => 'admin@gmail.com',
            'role' => 1,
            'status' => 1,
        ]); // 1:Admin

        $advisor = User::where('id', 2)->first();
        $advisor->update(['role' => 2]);// 2:Advisor
        $advisor->assignRole(Role::findByName('advisor'));

        $agency = User::where('id', 3)->first();
        $agency->update(['role' => 3]); // 3:Agency
        $agency->assignRole(Role::findByName('agency'));

        $creative = User::where('id', 4)->first();
        $creative->assignRole(Role::findByName('creative'));

        $creative2 = User::where('id', 4)->first();
        $creative2->assignRole(Role::findByName('creative'));


        // ********************************************************
        // ******************** AGENCY USERS **********************
        // ********************************************************

        // $agency_users = User::where('role', 3)->get();

        // $jobIds = [];
        // $agency = Role::findByName('agency');
        // foreach ($agency_users as $user) {
        //     $user->assignRole($agency);

        //     $data_user_id = ['user_id' => $user->id];

        //     \App\Models\Agency::factory()->create($data_user_id);

        //     \App\Models\Message::factory(20)->create(['sender_id' => $user->id]);

        //     $addresses = \App\Models\Address::factory(1)->create(array_merge($data_user_id, ['label' => 'business']));

        //     \App\Models\Phone::factory(3)->create($data_user_id);

        //     \App\Models\Link::factory(4)->create($data_user_id);

        //     // \App\Models\Attachment::factory(3)->create($data_user_id);

        //     $jobs = \App\Models\Job::factory(10)->create($data_user_id);

        //     foreach ($jobs as $job) {
        //         $jobIds[] = $job->id;
        //     }

        // }

        // ********************************************************
        // ******************** CREATIVE USERS ********************
        // ********************************************************
        // $creative = Role::findByName('creative');
        // $creative_users = User::where('role', 4)->get();
        // foreach ($creative_users as $user) {
        //     $user->assignRole($creative);

        //     $data_user_id = ['user_id' => $user->id];

        //     \App\Models\Creative::factory()->create($data_user_id);

        //     \App\Models\Address::factory(1)->create(array_merge($data_user_id, ['label' => 'personal']));

        //     \App\Models\Link::factory(3)->create($data_user_id);

        //     // $attachments = \App\Models\Attachment::factory(3)->create($data_user_id);

        //     \App\Models\Education::factory(2)->create($data_user_id);
        //     \App\Models\Experience::factory(2)->create($data_user_id);

        //     for ($i = 0; $i < 3; $i++) {
        //         // $attachment_id = $attachments->random()->id;
        //         $application = \App\Models\Application::factory()->create([
        //             'user_id' => $user->id,
        //             'job_id' => $jobIds[array_rand($jobIds)],
        //             // 'attachment_id' => $attachment_id,
        //         ]
        //         );

        //         // \App\Models\Note::factory(1)->create([
        //         //     'user_id' => $user->id,
        //         //     'application_id' => $application->id,
        //         // ]
        //         // );

        //         // \App\Models\Bookmark::factory(1)->create([
        //         //     'user_id' => $user->id,
        //         //     'resource_id' => $jobIds[array_rand($jobIds)],
        //         // ]
        //         //);
        //     }
        // }

        // *******************************************************
        // ******************** ADVISOR USERS ********************
        // *******************************************************
        // $advisor = Role::findByName('advisor');
        // $advisor_users = User::where('role', 2)->get();
        // foreach ($advisor_users as $user) {
        //     $user->assignRole($advisor);

        //     \App\Models\Agency::factory()->create(
        //         [
        //             'user_id' => $user->id,
        //         ]);
        // }

        //Generate some more users
        // \App\Models\User::factory(15)->create();
        // \App\Models\Order::factory(15)->create();
        \App\Models\Group::factory(3)->create();

        \App\Models\Group::factory(1)->create(
            [
                'user_id' => 1,
                'name' => 'Feed',
                'status' => 'public',
            ]
        );

        $this->call(PostSeeder::class);
        $this->call(CommentSeeder::class);
        // $this->call(JobAlertSeeder::class);

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

        $this->call(TestUserSeeder::class);

        Artisan::call('import:users');
        Artisan::call('import:agencies');
        Artisan::call('import:creatives');
        Artisan::call('import:jobs');
        // Artisan::call('import:creative-spotlights');

        Artisan::call('optimize:clear');
    }
}
