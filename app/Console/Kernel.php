<?php

namespace App\Console;

use App\User;
use App\Jobs\MonitorReportCardChanges;
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
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Dispatch jobs to scan user's report card.
        $schedule->call(function(){

            $users = User::where('notify', true)
            ->where('suap_id', '!=', null)
            ->where('suap_key', '!=', 'null')->get();

            foreach ($users as $user) {
                dispatch(new MonitorReportCardChanges($user));
            }

            echo $users->count() . ' Jobs dispatched.\n';

        // })->everyMinute();
        })->everyTenMinutes();
        // })->everyThirtyMinutes();
        // })->hourly();

    }
}
