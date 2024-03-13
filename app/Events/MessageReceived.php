<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
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
        /**
         * php artisan queue:serve
         * php artisan websockets:serve
         */
        $receiver_id = $this->data['receiver_id'];
        $channel2 = 'messanger.'.$receiver_id;

        return [$channel2];
        // return new PrivateChannel($channel2); //Channel Name
    }

    public function broadcastAs()
    {
        return 'private_msg'; //Event Name
    }
}