<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class SendUnreadLoungeMentionEmail extends Command
{
    protected $signature = 'email:unread-mention-notification';

    protected $description = 'Send email with unread lounge mention notification';

    public function handle()
    {
        $date_range = now()->subDay();

        $unreadNotifications = Notification::whereDate('created_at', $date_range)
            ->where('type', 'lounge_menion')
            ->whereNull('read_at')
            ->get();


        foreach ($unreadNotifications as $notification) {

            $post = Post::find($notification->body);
            $group = $post->group;
            $receiver = User::find($notification->user_id);
            $author = $post->user;

            $data = [
                'data' => [
                    'recipient' => $receiver->first_name,
                    'name' => $author->full_name,
                    'inviter' => $author->full_name,
                    'inviter_profile_url' =>sprintf("%s/creative/%s", env('FRONTEND_URL'), $author?->username),
                    'profile_picture' => get_profile_picture($author),
                    'user' => $author,
                    'group_url' => sprintf("%s/groups/%s", env('FRONTEND_URL'), $group?->uuid),
                    'group' => $group->name,
                    'post_time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                ],
                'receiver' => $receiver
            ];

            SendEmailJob::dispatch($data, 'user_mentioned_in_post');
        }
    }
}