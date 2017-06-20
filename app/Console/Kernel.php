<?php

namespace App\Console;

use Artisan;
use App\Jobs\MonitorReportCardChanges;
use App\User;
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
        Commands\UpdateUserReportCard::class,
        Commands\RefreshTokens::class,
        Commands\UpdateSchoolYear::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Update the access token of every user.
        $schedule->call(function () {
            Artisan::call('suapbot:refreshtoken', ['--all' => true]);
        })->dailyAt('05:15');
    }
}
