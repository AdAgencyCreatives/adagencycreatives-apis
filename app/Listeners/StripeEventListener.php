<?php

namespace App\Listeners;

use App\Jobs\SendEmailJob;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    public function __construct()
    {
        //
    }

    public function handle(WebhookReceived $event)
    {
        if ($event->payload['type'] === 'checkout.session.completed') {

            $data = $event->payload['data'];
            app('log')->channel('stripe_payments')->info($data);

            $email = $data['object']['customer_details']['email'];
            $total_amount_of_package = $data['object']['amount_subtotal'];
            $total_amount_of_package = $total_amount_of_package / 100;
            $current_paid_amount = $data['object']['amount_total']; // This can be discounted amount if user used coupon
            $current_paid_amount = $current_paid_amount / 100; // This can be discounted amount if user used coupon

            $user = User::whereEmail($email)->first();
            //we are deciding user pkg based on the price of the pkg, if price x, then pkg x, if price y, then pkg y
            $plan = Plan::where('price', $total_amount_of_package)->first();
            $totalQuota = $plan->quota;
            $endDate = Carbon::now()->addDays($plan->days);

            $user->subscriptions()->create([
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
                'total' => $current_paid_amount,
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
