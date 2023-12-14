<?php

namespace App\Console\Commands;


use App\Jobs\SendEmailJob;
use App\Models\Api\V1\JobInvitation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendJobInvitationEmail extends Command
{
    protected $signature = 'email:pending-job-invitation';

    protected $description = 'Send email about pending job invitations to users';

    public function handle()
    {
        $date_range = now()->subDay();

        $unread_invitations = JobInvitation::where('created_at', '>', $date_range)
            ->whereNull('read_at')
            ->select('creative_id', DB::raw('count(*) as message_count'))
            ->groupBy('creative_id')
            ->get();

        foreach ($unread_invitations as $unread_invitation) {

            $creative_id = $unread_invitation->creative_id;
            $recipient = User::where('id', $creative_id)->first();
            $unreadMessageCount = $unread_invitation->message_count;

            // Get the oldest contacts who sent messages to the user
            $oldestmessages = JobInvitation::select('user_id', DB::raw('MIN(created_at) as max_created_at'))
                ->where('creative_id', $creative_id)
                ->whereNull('read_at')
                ->where('created_at', '>', $date_range)
                ->groupBy('user_id')
                ->take(5)
                ->orderBy('max_created_at', 'desc')
                ->get();
// dd($oldestmessages);
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
