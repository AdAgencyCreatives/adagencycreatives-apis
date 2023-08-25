<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $subscriptions = $user->subscriptions()->get();

        return $subscriptions;
    }

    public function show(Plan $plan, Request $request)
    {
        $intent = auth()->user()->createSetupIntent();

        return [
            'intent' => $intent,
            'plan' => $plan,
        ];
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

    public function cancel(Request $request)
    {
        $user = $request->user();
        foreach ($user->subscriptions as $subscription) {
            $subscription->cancelNow();
        }

        return [
            'message' => 'Subscriptions cancelled',
        ];
    }
}
