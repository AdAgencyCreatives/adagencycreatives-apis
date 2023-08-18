<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        Artisan::call('adagencycreatives:permission');

        \App\Models\User::factory(15)->create();

        User::where('id', '<', 5)->update(['role' => 3]); // 3:Agency
        User::where('id', '>', 10)->update(['role' => 2]); // 2:Advisor
        User::where('id', 1)->update(['role' => 1]); // 1:Admin

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

            $addresses = \App\Models\Address::factory(1)->create($data_user_id);

            \App\Models\Phone::factory(3)->create($data_user_id);

            \App\Models\Link::factory(3)->create($data_user_id);

            \App\Models\Attachment::factory(3)->create($data_user_id);

            foreach ($addresses as $address) {
                $jobs = \App\Models\Job::factory(2)->create([
                    'user_id' => $user->id,
                    'address_id' => $address->id,
                ]
                );

                foreach ($jobs as $job) {
                    $jobIds[] = $job->id;
                }
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

            \App\Models\Address::factory(1)->create($data_user_id);

            \App\Models\Link::factory(3)->create($data_user_id);

            $attachments = \App\Models\Attachment::factory(3)->create($data_user_id);

            $resumes = \App\Models\Resume::factory(3)->create($data_user_id);

            foreach ($resumes as $resume) {
                $data = ['resume_id' => $resume->id];
                \App\Models\Education::factory(2)->create($data);
                \App\Models\Experience::factory(2)->create($data);
            }

            for ($i = 0; $i < 3; $i++) {
                $application = \App\Models\Application::factory()->create([
                    'user_id' => $user->id,
                    'job_id' => $jobIds[array_rand($jobIds)],
                    'attachment_id' => $attachments->random()->id,
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
        }

        // ********************************************************
        // ******************** GENERAL SEEDERS *******************
        // ********************************************************
        $this->call(CategorySeeder::class);
        $this->call(IndustrySeeder::class);

        //Generate some more users
        \App\Models\User::factory(15)->create();
    }
}
