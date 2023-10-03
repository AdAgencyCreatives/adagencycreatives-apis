<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $filePath = public_path('locations.json');
        if (File::exists($filePath)) {
            $jsonData = File::get($filePath);
            $citiesAndStates = json_decode($jsonData, true);
            foreach ($citiesAndStates as $item) {

                $state = Location::factory()->create([
                    'name' => $item['state'],
                    'slug' => Str::slug($item['state']),
                ]);
                foreach ($item['cities'] as $city) {

                    Location::factory()->create([
                        'name' => $city,
                        'slug' => Str::slug($city),
                        'parent_id' => $state->id,
                    ]);
                }
            }

        }
    }
}
