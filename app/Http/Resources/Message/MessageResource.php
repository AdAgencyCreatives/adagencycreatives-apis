<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'sender_id' => $this->sender->id,
            'receiver_id' => $this->receiver->id,
            'message' => $this->message,
            'type' => ($this->sender_id === $user->uuid) ? 'sent' : 'received',
            'created_at' => $this->created_at->format(config('global.datetime_format')),
        ];
    }
}
