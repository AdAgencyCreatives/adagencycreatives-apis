<?php

namespace App\Listeners;

use App\Jobs\SendEmailJob;
use App\Models\Plan;
use App\Models\User;
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
            $email = $data['object']['customer_details']['email'];
            $amount = $data['object']['amount_total'];
            $metadata = $data['object']['metadata']['plan'] ?? null;

            $user = User::whereEmail($email)->first();
            $plan = Plan::where('slug', $metadata)->first();

            $order = $user->orders()->create([
                'plan_id' => $plan->id,
                'amount' => $amount, //Amount from API response, it can be discounted price if user used any coupon code
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