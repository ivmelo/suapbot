<?php

namespace App\Console\Commands;

use Bugsnag;
use App\Telegram\Tools\Markify;
use App\User;
use Illuminate\Console\Command;
use Ivmelo\SUAP\SUAP;

class UpdateUserReportCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suapbot:updatereportcard {user_id?} {--all} {--notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a user report card.';

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
        $notify = $this->option('notify');

        // --all flag. Update all users.
        if ($this->option('all')) {

            // Grab all.
            $this->info('All students...');
            $users = User::with('settings', 'report_card')->hasSuapCredentials()->get();

            if ($users->count() < 1) {
                $this->info('No users.');
            } else {
                // Create a progress bar.
                $bar = $this->output->createProgressBar(count($users));

                // Iterate and update.
                foreach ($users as $user) {
                    $status = $user->report_card->doUpdate($notify);
                    $this->info('#' . $user->id . ' | STATUS: ' . $status);
                    // Update progress bar.
                    $bar->advance();
                }

                $bar->finish();
            }
        } else {
            // Find the lucky user.
            $user = User::find($this->argument('user_id'));

            // Update user data if found.
            if ($user) {
                $status = $user->report_card->doUpdate($notify);
                $this->info('#' . $user->id . ' | STATUS: ' . $status);
            } else {
                $this->error('User not found!');
            }
        }
    }
}
