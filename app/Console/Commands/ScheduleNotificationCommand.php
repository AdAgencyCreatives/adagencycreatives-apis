<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\ScheduleNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ScheduleNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adagencycreatives:schedule-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users after every 15 minutes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifcations = ScheduleNotification::whereStatus(ScheduleNotification::STATUSES['PENDING'])->where('scheduled_at', '>=', now())->get();
        if (!$notifcations->isEmpty() && $notifcations->count() > 0) {
            foreach ($notifcations as $notifcation) {
                $store = Notification::create([
                    'uuid' => Str::uuid(),
                    'user_id' => $notifcation->recipient_id,
                    'type' => $notifcation->type,
                    'message' => $notifcation->notification_text,
                    'body' => [],
                ]);
                if ($store) {
                    $notifcation->update(['status' => ScheduleNotification::STATUSES['DELIVERED']]);
                }
            }
        }
        return Command::SUCCESS;
    }
}
