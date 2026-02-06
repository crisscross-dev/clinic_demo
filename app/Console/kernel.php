<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule consent form processing every minute
        $schedule->command('consent:process-schedules')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('students:deactivate-inactive')->daily(); // deactivate inactive students

        $schedule->command('students:cleanup-unapproved')->daily(); // delete unapproved students

        $schedule->command('pending:clean-expired')->daily(); // clean expired pending registrations

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        // Optional: inline inspire command
        $this->command('inspire', function ($command) {
            $command->comment(\Illuminate\Foundation\Inspiring::quote());
        })->purpose('Display an inspiring quote');
    }
}
