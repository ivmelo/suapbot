<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UpdateSchoolYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suapbot:updateschoolyear {userId?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates current school year and term for one or all students.';

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
        if ($this->option('all')) {
            // Update school year for all users.
            $users = User::hasSuapCredentials()->get();

            $bar = $this->output->createProgressBar(count($users));

            $this->info('Updating school year for all users...');

            foreach ($users as $user) {
                if (!$user->updateSchoolYear()) {
                    $this->error('Error updating school year for user #'.$user->id);
                }
                $bar->advance();
            }

            $bar->finish();
        } else {
            // Refresh for the specified user only.
            $user = User::find($this->argument('userId'));

            if (!$user) {
                $this->error('User not found!');
            } else {
                if ($user->updateSchoolYear()) {
                    $this->info('School year updated!');
                } else {
                    $this->error('Error updating school year for user #'.$user->id);
                }
            }
        }
    }
}
