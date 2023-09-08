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
        $this->call(LocationSeeder::class);

        Artisan::call('adagencycreatives:permission');

        \App\Models\User::factory(15)->create();

        User::where('id', '<', 5)->update(['role' => 3]); // 3:Agency
        User::where('id', '>', 10)->update(['role' => 2]); // 2:Advisor
        User::where('id', 1)->update([
            'email' => 'admin@gmail.com',
            'role' => 1,
        ]); // 1:Admin

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

            \App\Models\Link::factory(4)->create($data_user_id);

            \App\Models\Attachment::factory(3)->create($data_user_id);

            foreach ($addresses as $address) {
                $jobs = \App\Models\Job::factory(10)->create([
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
        $this->call(YearsOfExperienceSeeder::class);
        $this->call(PlansTableSeeder::class);

        //Generate some more users
        \App\Models\User::factory(15)->create();
        \App\Models\Order::factory(15)->create();
        \App\Models\Group::factory(3)->create();

        $this->call(PostSeeder::class);
        $this->call(CommentSeeder::class);
    }
}
