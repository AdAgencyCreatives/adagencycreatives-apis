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
        $date_range = now()->subDay();

        $unreadMessages = Message::where('created_at', '>', $date_range)
            ->where('type', 'private')
            ->whereNull('read_at')
            ->select('receiver_id', DB::raw('count(*) as message_count'))
            ->groupBy('receiver_id')
            ->get();

        foreach ($unreadMessages as $unreadMessage) {

            $recipient = $unreadMessage->receiver;
            $unreadMessageCount = $unreadMessage->message_count;

            // Get the oldest contacts who sent messages to the user
            $oldestmessages = Message::select('sender_id', DB::raw('MIN(created_at) as max_created_at'))
                ->where('receiver_id', $unreadMessage->receiver_id)
                ->whereNull('read_at')
                ->where('created_at', '>', $date_range)
                ->groupBy('sender_id')
                ->take(5)
                ->orderBy('max_created_at', 'desc')
                ->with('sender')
                ->get();

            $recent_messages = [];
            foreach ($oldestmessages as $msg) {
                $recent_messages[] = [
                    'name' => $msg->sender->first_name,
                    'profile_url' => env('FRONTEND_URL').'/profile/'.$msg->sender->id,
                    'profile_picture' => get_profile_picture($msg->sender),
                    'message_time' => \Carbon\Carbon::parse($msg->max_created_at)->diffForHumans(),
                ];
            }

            $data = [
                'recipient' => $recipient->first_name,
                'unread_message_count' => $unreadMessageCount,
                'recent_messages' => $recent_messages,
            ];

            SendEmailJob::dispatch([
                'receiver' => $recipient,
                'data' => $data,
            ], 'unread_message');

        }
    }
}