<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Attachment;
use App\Models\Creative;
use App\Models\Location;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class CheckMissingLocations extends Command
{
    protected $signature = 'check:missing-locations';

    protected $description = 'It imports only creative portfolio items';

    protected function configure()
    {
        $this->addArgument('startIndex', InputArgument::OPTIONAL, 'Description of startIndex argument');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Description of startIndex argument');
    }

    public function handle()
    {
        $states = Location::whereNull('parent_id')->get();

        foreach ($states as $key => $state) {
            $city = Location::where('parent_id', $state->id)->first();
            if(!$city) continue;

            $addresses = Address::where('state_id', $state->id)->where('city_id', $city->id)->pluck('user_id')->toArray();

            $creatives = Creative::whereIn('user_id',$addresses )->get();
            foreach ($creatives as $key => $creative) {
                dump(sprintf("%s", $creative->user_id));
            }
        }

    }


}
