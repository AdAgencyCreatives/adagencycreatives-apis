<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\FriendRequest;
use App\Models\Friendship;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendFriendRequestEmails extends Command
{

    protected $signature = 'email:send-friend-request-emails';
    protected $description = 'It will send friend request status emails to users who have not responded to friend request in last 24 hours.';

    public function handle()
    {
        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->with('sender', 'receiver')
            ->get();

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            if ($sender->role == 'creative') {
                $profile_url = '/creative/' . $sender->creative?->slug ?? '';
            } elseif ($sender->role == 'agency') {
                $profile_url = '/agency/' . $sender->agency?->slug ?? '';
            } else {
                $profile_url = $sender->username;
            }

            SendEmailJob::dispatch([
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'inviter' => $sender->first_name,
                    'iniviter_profile' => sprintf("%s%s", env('FRONTEND_URL'), $profile_url),
                ],
            ], 'friendship_request_sent');
        }
    }
}