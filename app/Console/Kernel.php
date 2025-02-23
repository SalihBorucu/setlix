<?php

namespace App\Console;

use App\Console\Tasks\TrialTasks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These schedules are used to run console commands.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Handle trial-related tasks
        $trialTasks = new TrialTasks();
        
        $schedule->call([$trialTasks, 'cleanupExpiredTrials'])->daily();
        $schedule->call([$trialTasks, 'sendExpirationNotifications'])->daily();
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