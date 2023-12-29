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
            'id' => $this->id,
            'sender_id' => $sender1->uuid,
            'receiver_id' => $this->receiver->uuid,
            'sender_name' => $sender1->full_name,
            'message' => $this->message,
            'type' => $this->type,
            'message_type' => $this->getMessageType($sender1, $current_user),
            'created_at' => $this->created_at,
            'human_readable_date' => $this->created_at->diffForHumans(),
        ];
    }

    private function getMessageType($sender1, $current_user)
    {
        return $sender1->uuid === $current_user->uuid ? 'sent' : 'received';
    }
}