<?php

namespace App\Console;

use App\Console\Tasks\TrialTasks;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\UpdateExpiredSubscriptionsStatus;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateGeoLite2Database::class,
        Commands\TestGeolocation::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * These schedules are used to run console commands.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Update GeoLite2 database weekly
        $schedule->command('geolite2:update')->weekly()->runInBackground();
        
        // Handle trial-related tasks
        $trialTasks = new TrialTasks();
        $trialTasks->schedule($schedule);

        $schedule->job(new UpdateExpiredSubscriptionsStatus)->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 