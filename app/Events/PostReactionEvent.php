<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostReactionEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $data;

    public function __construct($data = ['message' => 'New Reaction'])
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        $channel = 'community';

        return [$channel];
    }

    public function broadcastAs()
    {
        return 'post_reaction'; //Event Name
    }
}