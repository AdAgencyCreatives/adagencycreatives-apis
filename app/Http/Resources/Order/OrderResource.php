<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user->first_name.' '.$this->user->last_name,
            'plan' => $this->plan->name,
            'amount' => '$'.$this->amount,
            'created_at' => $this->created_at->format(config('global.datetime_format')),
            'updated_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
