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
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                    'message_type' => $this->getMessageType($message),
                ];
            }),
        ];
    }

    private function getMessageType($message)
    {
        return $message->sender_id === $this->loggedInUserId ? 'sent' : 'received';
    }
}
