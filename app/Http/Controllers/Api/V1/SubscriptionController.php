<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Jobs\SendEmailJob;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
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

            $subscriptionBuilder = $user->newSubscription($plan->slug, $plan->stripe_plan);

            // Check if a coupon code was provided and apply it
            if ($request->has('coupon_code')) {

            }
            $subscriptionBuilder->withCoupon('TEST40    ');
            $subscription = $subscriptionBuilder->allowPromotionCodes()->create($request->token);

            $totalQuota = $plan->quota;
            $endDate = Carbon::now()->addDays($plan->days);

            $subscription->update([

                'quota_left' => $totalQuota,
                'ends_at' => $endDate,
            ]);

            $order = $user->orders()->create([
                'plan_id' => $plan->id,
                'amount' => $plan->price,
            ]);

            $data = [
                'order_no' => $order->id,
                'username' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
                'total' => $plan->price,
                'pm_type' => $subscription->owner->pm_type,
                'created_at' => \Carbon\Carbon::parse($subscription->created_at)->format('F d, Y'),
            ];

            $admin = User::find(1);
            SendEmailJob::dispatch([
                'receiver' => $admin,
                'data' => $data,
            ], 'order_confirmation');

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

    public function status(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription;

        if ($user->subscription) {
            return new SubscriptionResource($subscription);
        } else {
            return response()->json([], 404);
        }
    }

    public function packages()
    {
        return Plan::all();
    }

    public function webhook(Request $request)
    {
        if ($request->type === 'checkout.session.completed') {

            $data = $request->data;
            app('log')->channel('stripe_payments')->info($data);

            $email = $data['object']['customer_details']['email'];
            $total_amount_of_package = $data['object']['amount_subtotal'];
            $total_amount_of_package = $total_amount_of_package / 100;
            $current_paid_amount = $data['object']['amount_total']; // This can be discounted amount if user used coupon

            $user = User::whereEmail($email)->first();
            $plan = Plan::where('price', $total_amount_of_package)->first();

            $totalQuota = $plan->quota;
            $endDate = Carbon::now()->addDays($plan->days);

            $user->subscription()->create([
                'name' => $plan->slug,
                'price' => $current_paid_amount,
                'quantity' => $totalQuota,
                'quota_left' => $totalQuota,
                'ends_at' => $endDate,
            ]);

            $order = $user->orders()->create([
                'plan_id' => $plan->id,
                'amount' => $current_paid_amount, //Amount from API response, it can be discounted price if user used any coupon code
            ]);

            $data = [
                'order_no' => $order->id,
                'username' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
                'total' => $plan->price,
                'pm_type' => '',
                'created_at' => \Carbon\Carbon::now()->format('F d, Y'),
            ];

            $admin = User::find(1);
            SendEmailJob::dispatch([
                'receiver' => $admin,
                'data' => $data,
            ], 'order_confirmation');
        }
    }
}
