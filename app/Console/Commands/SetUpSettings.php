<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class SetUpSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suapbot:setupsettings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the settings object for each user.';

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
        $users = User::all();

        foreach ($users as $user) {
            if (!$user->settings) {
                $user->settings()->create([
                    'grades'     => true,
                    'classes'    => true,
                    'attendance' => true,
                ]);
            }
        }
    }
}
