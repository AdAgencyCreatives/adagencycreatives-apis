<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendUnreadMessageEmail extends Command
{
    protected $signature = 'email:unread-message-count';
    protected $description = 'Send email with unread message counts to users';

    public function handle()
    {
        $unreadMessages = Message::whereNull('read_at')
        ->select('receiver_id', DB::raw('count(*) as message_count'))
        ->groupBy('receiver_id')
        ->get();

        foreach ($unreadMessages as $unreadMessage) {
            $recipient = $unreadMessage->receiver;
            $unreadMessageCount = $unreadMessage->message_count;

            $data = [
                'recipient' => $recipient->first_name,
                'message_sender_name' => $recipient->first_name,
                'message_sender_profile_url' => get_profile_picture($recipient),
                'message_count' => $unreadMessageCount,
                'profile_url' => env('FRONTEND_URL') . '/profile/',
            ];

            SendEmailJob::dispatch([
                'receiver' => $recipient,
                'data' => $data,
            ], 'unread_message');

        }
    }
}
