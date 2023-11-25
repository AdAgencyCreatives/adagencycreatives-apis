<?php

namespace App\Console\Commands;

use App\Models\FriendRequest;
use App\Models\Friendship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendFriendRequestEmails extends Command
{

    protected $signature = 'send-friend-request-emails';
    protected $description = 'It will send friend request status emails to users who have not logged in in 24 hours.';

    public function handle()
    {
        // Get the oldest contacts who sent messages to the user
        $friendRequests = FriendRequest::select('sender_id', DB::raw('MIN(created_at) as max_created_at'))
            // ->where('status', 'pending')
            // ->where('created_at', '>=', \Carbon\Carbon::now()->subDay())
            ->groupBy('sender_id')
            ->orderBy('max_created_at', 'asc')
            ->get();


        dd($friendRequests->toArray());
        $unreadMessages = Friendship::whereNull('read_at')
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
                ->groupBy('sender_id')
                ->take(5)
                ->orderBy('max_created_at', 'asc')
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


        }
    }

}