<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DateTimeZone;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $daily_time = "10:00";
        $schedule->command('telescope:prune --hours=480')->daily();
        $schedule->command('email:unread-message-count')->dailyAt($daily_time);
        // $schedule->command('email:unread-message-count72')->dailyAt($daily_time);
        // $schedule->command('email:unread-message-count240')->dailyAt($daily_time);
        $schedule->command('job-post-expiring')->dailyAt($daily_time);
        $schedule->command('email:unread-mention-notification')->dailyAt($daily_time);
        $schedule->command('adagencycreatives:schedule-notifications')->everyFifteenMinutes();
        // $schedule->command('portfolio_latest:generate')->everyMinute();

        $schedule->command('email:send-pending-friend-request-emails')->dailyAt($daily_time);

        $schedule->command('email:send-job-closed-emails')->dailyAt($daily_time);
        // $schedule->command('email:send-admin-friend-request-emails')->everyMinute();
        $schedule->command('welcome-next-queued-creative')->dailyAt("10:00");
        $schedule->command('welcome-next-queued-creative')->dailyAt("11:00");
        $schedule->command('welcome-next-queued-creative')->dailyAt("12:00");

        $schedule->command('remind-profile-completion-creative')->everyFifteenMinutes();
        $schedule->command('remind-profile-completion-agency')->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'America/Chicago';
    }
}
