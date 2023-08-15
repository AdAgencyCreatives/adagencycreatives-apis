<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();

        User::where('id', '<', 5)->update(['role' => 3]);

        // ********************************************************
        // ******************** AGENCY USERS **********************
        // ********************************************************

        $agency_users = User::where('role', 3)->pluck('id');

        $jobIds = [];

        foreach ($agency_users as $user) {
            \App\Models\Agency::factory()->create([
                'user_id' => $user,
            ]);

            $addresses = \App\Models\Address::factory(1)->create(
                ['user_id' => $user]
            );

            \App\Models\Phone::factory(3)->create(
                ['user_id' => $user]
            );

            \App\Models\Link::factory(3)->create(
                ['user_id' => $user]
            );

            \App\Models\Attachment::factory(3)->create(
                ['user_id' => $user]
            );

            foreach ($addresses as $address) {
                $jobs = \App\Models\Job::factory(2)->create([
                    'user_id' => $user,
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

        $creative_users = User::where('role', 4)->pluck('id');
        foreach ($creative_users as $user) {
            $data_user_id = ['user_id' => $user];

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
                    'user_id' => $user,
                    'job_id' => $jobIds[array_rand($jobIds)],
                    'attachment_id' => $attachments->random()->id,
                ]
                );

                \App\Models\Note::factory(1)->create([
                    'user_id' => $user,
                    'application_id' => $application->id,
                ]
                );

                \App\Models\Bookmark::factory(1)->create([
                    'user_id' => $user,
                    'resource_id' => $jobIds[array_rand($jobIds)],
                ]
                );
            }

        }

        // ********************************************************
        // ******************** GENERAL SEEDERS *******************
        // ********************************************************
        $this->call(CategorySeeder::class);
        $this->call(IndustrySeeder::class);

    }
}