<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageReceived;
use App\Http\Controllers\Controller;

class WebSocketController extends Controller
{
    public function index()
    {
        event(new MessageReceived);
    }
}
