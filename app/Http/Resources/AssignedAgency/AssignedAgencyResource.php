<?php

namespace App\Http\Resources\AssignedAgency;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignedAgencyResource extends JsonResource
{
    public function toArray($request)
    {
        $agency = $this->agency;
        $user = $this->user;

        return [
            'id' => $this->uuid,
            'agency_id' => $user->uuid, //uuid from users table
            'agency_name' => $agency->name,
            'logo' => get_profile_picture($user),
            // 'impersonate_url' => route('advisor.impersonate', $user->uuid),
            'status' => $this->get_subscription_status($user),
        ];
    }

    protected function get_subscription_status($user)
    {
        $subscription = $user->active_subscription;

        if ($subscription) {

            return [
                'name' => $subscription->name,
                'quota_left' => $subscription->quota_left,
                'ends_at' => $subscription->ends_at,
                'status' => $this->getStatus($subscription),
            ];
        } else {
            // If no active subscription, return all keys with default values
            return [
                'name' => null,
                'quota_left' => null,
                'ends_at' => null,
                'status' => 'expired',
            ];
        }
    }

    protected function getStatus($subscription)
    {
        $endsAtDate = Carbon::parse($subscription->ends_at);
        if ($endsAtDate->isPast()) {
            return 'expired';
        }

        return 'active';
    }
}
