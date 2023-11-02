<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $data;

    public function __construct($data = 'Default Message')
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        $receiver_id = $this->data['receiver_id'];
        $channel2 = 'messanger.'.$receiver_id;

        return [$channel2];
    }

    public function broadcastAs()
    {
        return 'notification'; //Event Name
    }
}