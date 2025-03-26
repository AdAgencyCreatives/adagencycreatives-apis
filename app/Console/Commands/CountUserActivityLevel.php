<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Creative;
use App\Models\CreativeCache;
use App\Models\Message;
use App\Models\PostReaction;
use Illuminate\Console\Command;

class CountUserActivityLevel extends Command
{
    protected $signature = 'count:user-activity';
    protected $description = 'It calculates the user activity on separate thread';

    public function handle()
    {
        CreativeCache::truncate();

        $creatives = Creative::with('user', 'category', 'user.addresses', 'user.addresses.state', 'user.addresses.city')->whereNotNull('category_id')->select('id', 'user_id', 'category_id', 'created_at')->latest()->get();

        $max_messages = Message::select(\DB::raw('count(*) as message_count'))
            ->groupBy('sender_id')
            ->orderByDesc('message_count')
            ->limit(1)
            ->value('message_count');

        $max_applications = Application::select(\DB::raw('count(*) as application_count'))
            ->groupBy('user_id')
            ->orderByDesc('application_count')
            ->limit(1)
            ->value('application_count');

        $max_posts = PostReaction::select(\DB::raw('count(*) as post_count'))
            ->groupBy('user_id')
            ->orderByDesc('post_count')
            ->limit(1)
            ->value('post_count');

        $cacheData = [];

        foreach ($creatives as $creative) {

            $category = $creative->category?->name;
            $user = $creative->user;
            $location = get_location_text($user);

            $cacheData[] = [
                'creative_id' => $creative->id,
                'category' => $category,
                'location' => $location,
                'activity_rank' => calculate_activity_score($creative->user_id, $max_messages, $max_applications, $max_posts),
                'created_at' => $creative->created_at,
            ];
        }

        CreativeCache::insert($cacheData);

        return 0;
    }
}