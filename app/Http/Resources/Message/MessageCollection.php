<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageCollection extends ResourceCollection
{
    private $loggedInUserId;

    public function __construct($resource, $loggedInUserId)
    {
        parent::__construct($resource);
        $this->loggedInUserId = $loggedInUserId;
    }

    public function toArray($request)
    {
        return [
            'messages' => $this->collection->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender->uuid,
                    'receiver_id' => $message->receiver->uuid,
                    'message' => $message->message,
                    'message_type' => $this->getMessageType($message),
                    'created_at' => $message->created_at,
                ];
            }),
        ];
    }

    private function getMessageType($message)
    {
        return $message->sender_id === $this->loggedInUserId ? 'sent' : 'received';
    }
}