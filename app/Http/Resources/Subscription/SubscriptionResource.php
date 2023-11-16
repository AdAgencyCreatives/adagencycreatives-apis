<?php

namespace App\Http\Resources\Subscription;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'quota_left' => $this->quota_left,
            'ends_at' => $this->ends_at,
            'status' => $this->getStatus(),
        ];
    }

    protected function getStatus()
    {
        $endsAtDate = Carbon::parse($this->ends_at);
        if($endsAtDate->isPast()) {
            return 'expired';
        }

        return 'active';
    }
}