<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class SendAdminFriendRequestEmails extends Command
{

    protected $signature = 'email:send-admin-friend-request-emails';
    protected $description = 'It will send friend request emails from admin to users to responded admin friend request.';

    public function handle()
    {
        $admin_id = 202;
        $batch_size = 50;

        $sender = User::where('id', $admin_id)->first();

        $existing = FriendRequest::where('sender_id', $admin_id)->get(['receiver_id'])->all();
        $exclude_list = [$admin_id];

        for ($i = 0; $i < count($existing); $i++) {
            $exclude_list[] = $existing[$i]->receiver_id;
        }

        $receivers = User::where('role', '4')->whereNotIn('id', $exclude_list)->take($batch_size)->get();

        $now = now();
        $desired_from = Carbon::parse('2024-06-11 00:00:00');
        $desired_to = Carbon::parse('2024-06-11 11:59:59');

        $this->info('Time Now: ' . $now->format('Y-m-d H:i:s'));
        $this->info('Execution Allowed Between: ' . $desired_from->format('Y-m-d H:i:s') . " and " . $desired_to->format('Y-m-d H:i:s'));
        $this->info('Valid Receivers: ' . count($receivers) . ' Receivers');
        $this->info('Excluded: ' . count($exclude_list));

        if (!$now->greaterThanOrEqualTo($desired_from)) {
            $this->info('Can\'t execute before: ' . $desired_from->format('Y-m-d H:i:s'));
            return;
        }

        if (!$now->lessThanOrEqualTo($desired_to)) {
            $this->info('Can\'t execute after: ' . $desired_to->format('Y-m-d H:i:s'));
            return;
        }

        for ($i = 0; $i < count($receivers); $i++) {
            $receiver = $receivers[$i];

            // Check if a friendship already exists or a pending request
            $existingFriendship = FriendRequest::where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender)->where('receiver_id', $receiver);
            })->orWhere(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $receiver)->where('receiver_id', $sender);
            })->first();

            if (!$existingFriendship) {
                //Create a new friend request
                FriendRequest::create([
                    'uuid' => Str::uuid(),
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'status' => 'pending',
                    'date_created' => now(),
                    'date_updated' => now(),
                ]);
                $this->info('New friendship created between: ' . $sender->full_name . " and " . $receiver->full_name);
            } else if ($existingFriendship->status != 'pending' && $existingFriendship->status != 'accepted') {
                $existingFriendship->update([
                    'status' => 'pending',
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'date_updated' => now(),
                ]);
                $this->info('Existing friendship updated between: ' . $existingFriendship->sender->full_name . " and " . $existingFriendship->receiver->full_name);
            } else {
                $this->info('Pending friendship request between: ' . $existingFriendship->sender->full_name . " and " . $existingFriendship->receiver->full_name);
            }
        }

        $date_range = now()->format('Y-m-d');

        $friendRequests = FriendRequest::where(function ($q) use ($sender, $receiver) {
            $q->where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender)->where('receiver_id', $receiver);
            })->orWhere(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $receiver)->where('receiver_id', $sender);
            });
        })->where('status', 'pending')
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

        $this->info('Recipient Count: ' . count($receivers));

        foreach ($receivers as $receiver) {
            $senders = $bundle[$receiver->id];

            // SendEmailJob::dispatch([
            //     'receiver' => $receiver,
            //     'data' => [
            //         'recipient' => $receiver->first_name,
            //         'senders' => $senders,
            //         'multiple' => count($senders) > 1 ? "yes" : "no",
            //     ],
            // ], 'friendship_request_sent');

            $this->info('Sent friend request email to: ' . $receiver->full_name);
        }
    }
}