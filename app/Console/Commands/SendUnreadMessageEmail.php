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

        $unreadMessages = Message::whereDate('created_at', $date_range)
            ->whereIn('type', ['private', 'job'])
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
                ->whereIn('type', ['private', 'job'])
                ->whereNull('read_at')
                ->whereDate('created_at', $date_range)
                ->groupBy('sender_id')
                ->take(5)
                ->orderBy('max_created_at', 'desc')
                ->with('sender')
                ->get();

            $recent_messages = [];


            foreach ($oldestmessages as $msg) {
                $name = $msg?->sender?->first_name . ' ' . $msg?->sender?->last_name;
                $related = '';

                if ($msg?->sender?->agency) {
                    $name = $msg?->sender?->agency->name;
                    // $related = $msg?->sender?->first_name;
                } else if ($msg?->sender?->creative) {
                    // $related = $msg?->sender?->creative?->title;
                    if ($msg?->sender?->creative?->category?->name) {
                        $related = $msg?->sender?->creative?->category?->name;
                    }
                }

                $recent_messages[] = [
                    'name' => $name,
                    'profile_url' => env('FRONTEND_URL') . '/profile/' . $msg->sender->id,
                    'profile_picture' => get_profile_picture($msg->sender),
                    'message_time' => \Carbon\Carbon::parse($msg->max_created_at)->diffForHumans(),
                    'related' => $related,
                ];
            }

            $data = [
                'recipient' => $recipient->first_name,
                'unread_message_count' => $unreadMessageCount,
                'recent_messages' => $recent_messages,
                'date_range' => $date_range,
            ];

            SendEmailJob::dispatch([
                'receiver' => $recipient,
                'data' => $data,
            ], 'unread_message');
        }
    }
}
