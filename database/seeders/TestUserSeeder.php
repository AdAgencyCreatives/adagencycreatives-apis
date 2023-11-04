<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Creative;
use App\Models\Industry;
use App\Models\Media;
use App\Models\Strength;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        /**
         * Creative User
         */
        $creative_user_id = 4;
        $data_user_id = ['user_id' => $creative_user_id];
        \App\Models\Creative::factory()->create($data_user_id);

        User::where('id', $creative_user_id)->update([
            'first_name' => 'TEST',
            'last_name' => 'CREATIVE',
            'email' => 'creative@gmail.com',
            'role' => 4,
            'status' => 1,
            'status' => 1,
        ]);

        $industry_exp = Industry::where('id', '<', 6)->pluck('uuid')->toArray();
        $media_exp = Media::where('id', '<', 6)->pluck('uuid')->toArray();
        $strengths = Strength::where('id', '<', 6)->pluck('uuid')->toArray();

        $creative = Creative::where('user_id', $creative_user_id)->first();
        $creative->industry_experience = implode(',', $industry_exp);
        $creative->media_experience = implode(',', $media_exp);
        $creative->strengths = implode(',', $strengths);

        $creative->is_featured = 1;
        $creative->is_hybrid = 1;
        $creative->is_onsite = 1;
        $creative->save();

        $creative_user_id = 5;
        $data_user_id = ['user_id' => $creative_user_id];
        \App\Models\Creative::factory()->create($data_user_id);

        User::where('id', $creative_user_id)->update([
            'first_name' => 'TEST2',
            'last_name' => 'CREATIVE2',
            'email' => 'creative2@gmail.com',
            'role' => 4,
            'status' => 1,
            'status' => 1,
        ]);

        $creative = Creative::where('user_id', $creative_user_id)->first();
        $creative->industry_experience = implode(',', $industry_exp);
        $creative->media_experience = implode(',', $media_exp);
        $creative->strengths = implode(',', $strengths);

        $creative->is_featured = 1;
        $creative->is_hybrid = 1;
        $creative->is_onsite = 1;
        $creative->save();

        /**
         * Agency User
         */
        $agency_user_id = 3;
        $data_user_id = ['user_id' => $agency_user_id];
        \App\Models\Agency::factory()->create($data_user_id);

        User::where('id', $agency_user_id)->update([
            'email' => 'agency@gmail.com',
            'role' => 3,
            'status' => 1,
        ]);

        $agency = Agency::where('user_id', $agency_user_id)->first();
        $agency->name = 'TEST_AGENCY';
        $agency->slug = Str::slug($agency->name);
        $agency->industry_experience = implode(',', $industry_exp);
        $agency->media_experience = implode(',', $media_exp);
        $agency->is_featured = 1;
        $agency->is_hybrid = 1;
        $agency->is_onsite = 1;
        $agency->save();

        /**
         * Admin
         */

        $adeel_admin = User::where('email', 'adeelakhterit@gmail.com')->first();

    }
}