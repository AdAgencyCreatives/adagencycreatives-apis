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

        $creatives = Creative::with('user', 'category', 'user.addresses', 'user.addresses.state', 'user.addresses.city')->whereNotNull('category_id')->select('id','user_id', 'category_id', 'created_at')->latest()->get();

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
            $location = $this->get_location($user); 

            $cacheData[] = [
                'creative_id' => $creative->id,
                'category' => $category,
                'location' => $location,
                'activity_rank' => $this->calculate_activity_score($creative->user_id, $max_messages, $max_applications, $max_posts),
                'created_at' => $creative->created_at,  
                ];
        }

        CreativeCache::insert($cacheData);

        return 0;
    }

    private function calculate_activity_score($user_id, $max_messages, $max_applications, $max_posts )
    {
        $application_count = Application::where('user_id', $user_id)
            ->where('created_at', '>=', now()->subDays(14))
            ->count();

        $message_count = Message::where('sender_id', $user_id)
            ->where('created_at', '>=', now()->subDays(14))
            ->count();

        $post_reactions = PostReaction::where('user_id', $user_id)
            ->where('created_at', '>=', now()->subDays(14))
            ->count();

        $normalizedMessageScore = min(1, $message_count / $max_messages);
        $normalizedApplicationScore = min(1, $application_count / $max_applications);
        $normalizedPostScore = min(1, $post_reactions / $max_posts);

        $weightedApplicationScore = $normalizedApplicationScore * 0.40;
        $weightedMessageScore = $normalizedMessageScore * 0.30;
        $weightedPostScore = $normalizedPostScore * 0.30;

        $overallScore = $weightedMessageScore + $weightedApplicationScore + $weightedPostScore;

        $finalScore = round($overallScore * 100);

        return $finalScore;
    }

    private function get_location($user)
    {
        $address = $user->addresses ? collect($user->addresses)->firstWhere('label', 'personal') : null;

        if ($address) {
            $stateName = $address->state ? $address->state->name : null;
            $cityName = $address->city ? $address->city->name : null;
    
            $locationString = collect([$stateName, $cityName])
                ->filter() 
                ->implode(', '); 
    
            return $locationString;
        } else {
            return null;
        }
    }
}
