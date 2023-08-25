<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
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
        try {
            $plan = Plan::find($request->plan_id);
            $user = $request->user();

            $subscription = $user->newSubscription($plan->slug, $plan->stripe_plan)
                ->create($request->token);

            $totalQuota = $plan->quota;

            $subscription->update([
                'quota_left' => $totalQuota,
            ]);

            $user->orders()->create([
                'plan_id' => $plan->id,
                'amount' => $plan->price,
            ]);

            return $subscription;
        } catch (\Exception $e) {
            throw new ApiException($e, 'STRIPE-01');
        }
    }
}
