<?php

namespace App\Console;

use App\User;
use Artisan;
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
        Commands\SetUpSettings::class,
        Commands\SetUpReportCards::class,
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
        // Update the access token of every user twice, daily.
        $schedule->call(function () {
            Artisan::call('suapbot:refreshtoken', ['--all' => true]);
        })->dailyAt('05:15');

        $schedule->call(function () {
            Artisan::call('suapbot:refreshtoken', ['--all' => true]);
        })->dailyAt('17:15');

        // Compare user report cards every twenty minutes.
        $schedule->call(function () {
            Artisan::call('suapbot:updatereportcard', [
                '--all' => true,
                '--notify' => true
            ]);
        })->everyThirtyMinutes();
    }
}
