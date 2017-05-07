<?php

namespace App\Console\Commands;

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
        // --all flag. Update all users.
        if ($this->option('all')) {

            // Grab all.
            $this->info('All students...');
            $users = User::hasSuapCredentials()->get();

            if ($users->count() < 1) {
                $this->info('No users.');
            } else {
                // Create a progress bar for beautiful display.
                $bar = $this->output->createProgressBar(count($users));

                // Iterate and update.
                foreach ($users as $user) {
                    try {
                        $user->updateReportCard();
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }

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
                $user->updateReportCard();
            } else {
                $this->error('User not found!');
            }
        }
    }
}
