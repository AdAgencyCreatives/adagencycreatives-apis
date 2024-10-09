<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;

class SendUnreadLoungeMentionEmail extends Command
{
    /**
     * We had changed it to send 24 hours if unread - since its tagging. I think it's on a loom. When tagged in The Lounge,
     * person being tagged gets a notification on the right side of the dashboard and the notifications in The Lounge.
     * If they do not read the notification 24 hours later they get an email. What we changed, I think and could be wrong,
     * we stopped the 3 days and 10 days. Just one email 24 hours or 10a whichever.
     */
    protected $signature = 'email:unread-mention-notification';

    protected $description = 'Send email with unread lounge mention notification';

    public function handle()
    {
        $date_range = now()->subDay();

        $unreadNotifications = Notification::whereDate('created_at', $date_range)
            ->where('type', 'lounge_mention')
            ->whereNull('read_at')
            ->get();

        foreach ($unreadNotifications as $notification) {

            $post = Post::find($notification->body);
            $group = $post->group;
            $receiver = User::find($notification->user_id);
            $author = $post->user;

            $group_url = $group ? ($group->slug == 'feed' ? env('FRONTEND_URL') . '/community' : env('FRONTEND_URL') . '/groups/' . $group->uuid) : '';

            $data = [
                'data' => [
                    'recipient' => $receiver->first_name,
                    'name' => $author->full_name,
                    'inviter' => $author->full_name,
                    'inviter_profile_url' => sprintf("%s/creative/%s", env('FRONTEND_URL'), $author?->creative?->slug ?? $author?->username),
                    'profile_picture' => get_profile_picture($author),
                    'user' => $author,
                    'group_url' => $group_url,
                    'group' => $group->name,
                    'post_time' => \Carbon\Carbon::parse($post->created_at)->diffForHumans(),
                    'notification_uuid' => $notification->uuid,
                ],
                'receiver' => $receiver
            ];

            SendEmailJob::dispatch($data, 'user_mentioned_in_post');
        }
    }
}