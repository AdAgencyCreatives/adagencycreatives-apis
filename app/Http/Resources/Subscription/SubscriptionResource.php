<?php

namespace App\Http\Resources\Subscription;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        $endsAtDate = Carbon::parse($this->ends_at);

        $status = 'active';
        if ($this->quota_left < 1) {
            $status = 'expired';
        } elseif ($endsAtDate->isPast()) {
            $status = 'expired';
        }

        return [
            'name' => $this->name,
            'quota_left' => $this->quota_left,
            'ends_at' => $this->ends_at,
            'status' => $status,
        ];
    }
}
