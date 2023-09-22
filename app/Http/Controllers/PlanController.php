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
        $plan = Plan::find($request->plan_id);
        $user = $request->user();

        $subscription = $user->newSubscription($plan->slug, $plan->stripe_plan)
            ->create($request->token);

        dd($subscription);

        return $subscription;
    }
}
