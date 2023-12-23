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
            'sender_user' => $this->getUserName($sender1),
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

    private function getUserName($user)
    {
        if ($user->role != 'agency') {
            return $user->full_name;
        }

        return $user->agency ? ($user->agency->name ?? $user->full_name) : $user->full_name;

    }
}