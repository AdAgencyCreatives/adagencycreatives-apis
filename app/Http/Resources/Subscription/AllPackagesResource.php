<?php

namespace App\Http\Resources\Subscription;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AllPackagesResource extends JsonResource
{
    public function toArray($request)
    {
        $plan = $this->plan;

        return [
            'name' => $plan->name,
            'listing_duration' => $plan->days,
            'total_price' => sprintf('$%s.00', $plan->price),
            'paid_price' => sprintf('$%s.00', $this->price),
            'allowed_posts' => $plan->quota,
            'remaining_posts' => $this->quota_left,
            'purchase_date' => $this->created_at,
            'expiry_date' => $this->ends_at,
            'status' => $this->getStatus(),
        ];
    }


    public function getStatus()
    {
        $endsAtDate = Carbon::parse($this->ends_at);
        if($this->quota_left < 1){
            return 'Expired';
        }
        elseif($endsAtDate->isPast()){
            return 'Expired';
        }

        return 'Active';
    }
}
