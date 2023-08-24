<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function show(Plan $plan, Request $request)
    {
        $intent = auth()->user()->createSetupIntent();

        return view('subscription', compact('plan', 'intent'));
    }

    public function subscription(Request $request)
    {
        $subscription = $request->user()->newSubscription($request->plan_id, $request->stripe_plan)
            ->create($request->token);
        dd($subscription);

        return $subscription;
    }
}
