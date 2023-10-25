<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        $current_user = $request->user();
        $sender1 = $this->sender;

        return [
            'sender_id' => $sender1->uuid,
            'receiver_id' => $this->receiver->uuid,
            'message' => $this->message,
            'message_type' => $this->getMessageType($sender1, $current_user),
            'created_at' => $this->created_at,
        ];
    }

    private function getMessageType($sender1, $current_user)
    {
        return $sender1->uuid === $current_user->uuid ? 'sent' : 'received';
    }
}
