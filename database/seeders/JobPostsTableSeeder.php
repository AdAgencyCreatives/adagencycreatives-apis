<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobPosts = DB::table('job_posts')->orderBy('id')->get();
        $sortOrder = 1;

        foreach ($jobPosts as $jobPost) {
            DB::table('job_posts')
                ->where('id', $jobPost->id)
                ->update(['sort_order' => $sortOrder]);
            $sortOrder++;
        }
    }
}