<?php

namespace App\Http\Resources\Subscription;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        $currentDate = Carbon::now();
        $endsAtDate = Carbon::parse($this->ends_at);

        $status = ($endsAtDate->isPast()) ? 'expired' : 'active';

        return [
            'name' => $this->name,
            'quota_left' => $this->quota_left,
            'ends_at' => $this->ends_at,
            'status' => $status,
        ];
    }
}
