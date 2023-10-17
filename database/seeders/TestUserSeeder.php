<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Attachment;
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
        $creative_user_id = 15;
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

        Attachment::where('user_id', $creative_user_id)
            ->where('resource_type', 'profile_picture')->delete();
        Attachment::create([
            'uuid' => '41b9d69d-2f04-47fc-8735-2705591ecf3b',
            'user_id' => $creative_user_id,
            'resource_type' => 'profile_picture',
            'path' => 'profile_picture/41b9d69d-2f04-47fc-8735-2705591ecf3b/uZVJwdVoB6num2hPqhI097I2BTfj9OVJiC9HKIY8.jpg',
            'name' => 'avatar.jpg',
            'extension' => 'jpg',
        ]);

        /**
         * Agency User
         */
        $agency_user_id = 9;
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

        Attachment::create([
            'uuid' => '41b9d69d-2f04-47fc-8735-2705591ecf3c',
            'user_id' => $agency_user_id,
            'resource_type' => 'agency_logo',
            'path' => 'agency_logo/540c079d-0344-413f-b25e-dd12c3695a33/w6MGkMCmtQj4vUBzeEmtMQZkI9o1R4pIK5yBQNkY.png',
            'name' => 'unnamed (2).png',
            'extension' => 'png',
        ]);
    }
}
