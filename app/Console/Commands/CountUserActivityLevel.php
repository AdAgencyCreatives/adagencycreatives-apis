<?php

namespace App\Console\Commands;

use App\Models\Creative;
use App\Models\CreativeCache;
use Illuminate\Console\Command;

class CountUserActivityLevel extends Command
{
    protected $signature = 'count:user-activity';
    protected $description = 'It calculates the user activity on separate thread';

    public function handle()
    {
        CreativeCache::truncate(); 

        $creatives = Creative::with('user', 'category', 'user.addresses', 'user.addresses.state', 'user.addresses.city')->whereNotNull('category_id')->select('id','user_id', 'category_id', 'created_at')->latest()->get();
        
        $cacheData = []; 

        foreach ($creatives as $creative) {
          
            $category = $creative->category?->name;  
            $user = $creative->user; 
            $location = $this->get_location($user); 

            $cacheData[] = [
                'creative_id' => $creative->id,
                'category' => $category,
                'location' => $location,
                'activity_rank' => 0,
                'created_at' => $creative->created_at,  
                ];
        }

        CreativeCache::insert($cacheData);

        return 0;
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
