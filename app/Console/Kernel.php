<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ============================================================
        // Order QR System - Scheduled Tasks (CETAM Standards)
        // ============================================================

        // Clean expired orders daily at 2:00 AM
        // Removes delivered/cancelled orders older than retention period
        $schedule->command('orders:clean-expired')
            ->dailyAt('02:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-orders-cleanup.log'));

        // Check for expired payments daily at 8:00 AM
        // Automatically deactivates businesses with expired payments
        $schedule->command('payments:check-expired --deactivate --notify')
            ->dailyAt('08:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-payment-check.log'));

        // Send payment reminders 7 days before expiration at 9:00 AM
        $schedule->command('payments:send-reminders --days-before=7')
            ->dailyAt('09:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-payment-reminders.log'));

        // Send payment reminders 3 days before expiration at 9:00 AM
        $schedule->command('payments:send-reminders --days-before=3')
            ->dailyAt('09:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-payment-reminders.log'));

        // Send payment reminders 1 day before expiration at 9:00 AM
        $schedule->command('payments:send-reminders --days-before=1')
            ->dailyAt('09:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-payment-reminders.log'));

        // Generate weekly system report on Monday at 10:00 AM
        $schedule->command('system:report --period=7 --export=json')
            ->weeklyOn(1, '10:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-weekly-reports.log'));

        // Generate monthly system report on 1st of month at 10:00 AM
        $schedule->command('system:report --period=30 --export=json')
            ->monthlyOn(1, '10:00')
            ->timezone('America/Mexico_City')
            ->appendOutputTo(storage_path('logs/scheduled-monthly-reports.log'));

        // ============================================================
        // Optional: Laravel Framework Tasks
        // ============================================================

        // Prune old telescope entries (if using Laravel Telescope)
        // $schedule->command('telescope:prune')->daily();

        // Clear expired password reset tokens
        // $schedule->command('auth:clear-resets')->everyFifteenMinutes();
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
}
