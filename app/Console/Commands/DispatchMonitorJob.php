<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Jobs\MonitorReportCardChanges;

class DispatchMonitorJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:dispatch {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch a job to monitor a report card';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::find($this->argument('user_id'));

        if ($user) {
            dispatch(new MonitorReportCardChanges($user));
        } else {
            $this->error('User not found!');
        }
    }
}
