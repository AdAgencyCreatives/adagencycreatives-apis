<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendJobClosedEmails extends Command
{

    protected $signature = 'email:send-job-closed-emails';
    protected $description = 'It will send job closed emails to users who have applied on internal jobs.';

    // public function handle()
    // {
    //     $date_range = now()->subDay()->format('Y-m-d');

    //     $friendRequests = FriendRequest::where('status', 'pending')
    //         ->whereDate('updated_at', '=', $date_range)
    //         ->with('sender', 'receiver')
    //         ->get();

    //     foreach ($friendRequests as $fr) {
    //         $receiver = $fr->receiver;
    //         $sender = $fr->sender;

    //         if ($sender->role == 'creative') {
    //             $profile_url = '/creative/' . $sender->creative?->slug ?? '';
    //         } elseif ($sender->role == 'agency') {
    //             $profile_url = '/agency/' . $sender->agency?->slug ?? '';
    //         } else {
    //             $profile_url = $sender->username;
    //         }

    //         SendEmailJob::dispatch([
    //             'receiver' => $receiver,
    //             'data' => [
    //                 'recipient' => $receiver->first_name,
    //                 'inviter' => $sender->first_name,
    //                 'iniviter_profile' => sprintf("%s%s", env('FRONTEND_URL'), $profile_url),
    //             ],
    //         ], 'friendship_request_sent');
    //     }
    // }

    public function handle()
    {
        $date_range = now()->subDay()->format('Y-m-d');

        $friendRequests = FriendRequest::where('status', 'pending')
            ->whereDate('updated_at', '=', $date_range)
            ->orderBy('receiver_id')->orderByDesc('updated_at')
            ->get();

        $bundle = [];
        $receivers = [];

        foreach ($friendRequests as $fr) {
            $receiver = $fr->receiver;
            $sender = $fr->sender;

            $sender->profile_picture = get_profile_picture($sender);

            if (array_key_exists($receiver->id, $bundle)) {
                $bundle[$receiver->id][count($bundle)] = $sender;
            } else {
                $bundle[$receiver->id] = array(0 => $sender);
                $receivers[count($receivers)] = $receiver;
            }
        }

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];

            SendEmailJob::dispatch([
                'receiver' => $receiver,
                'data' => [
                    'recipient' => $receiver->first_name,
                    'senders' => $senders,
                    'multiple' => count($senders) > 1 ? "yes" : "no",
                ],
            ], 'friendship_request_sent');
        }
    }
}
