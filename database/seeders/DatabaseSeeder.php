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

        $agency_users = User::where('role', 3)->pluck('id');
        foreach ($agency_users as $user) {
            \App\Models\Agency::factory()->create([
                'user_id' => $user,
            ]);
        }

        $creative_users = User::where('role', 4)->pluck('id');
        foreach ($creative_users as $user) {
            \App\Models\Creative::factory()->create([
                'user_id' => $user,
            ]);
        }

    }
}
