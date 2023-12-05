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
        $schedule->command('telescope:prune --hours=720')->daily();
        $schedule->command('email:unread-message-count')->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

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
