<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {

        $agencies = User::where('role', 3)->get();

        foreach ($agencies as $agency) {
            $order = $agency->orders()->create([
                'plan_id' => [1, 2, 3][rand(0, 2)],
                'amount' => [149, 349, 649][rand(0, 2)],
            ]);
        }
    }
}
