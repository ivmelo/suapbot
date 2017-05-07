<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class RefreshTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suapbot:refreshtoken {userId?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a new SUAP access token for one or all users.';

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
        // Refresh for all users...
        if ($this->option('all')) {
            $users = User::hasSuapCredentials()->get();

            $bar = $this->output->createProgressBar(count($users));

            $this->info('Refreshing all user tokens...');

            foreach ($users as $user) {
                $this->refreshTokenFor($user);
                $bar->advance();
            }

            $bar->finish();
        } else {
            // Refresh for the specified user only.
            $user = User::find($this->argument('userId'));

            if (! $user) {
                $this->error('User not found!');
            } else {
                $refreshed = $this->refreshTokenFor($user);
                if ($refreshed) {
                    $this->info('Token refreshed!');
                } else {
                    $this->error('Token not refreshed!');
                }
            }
        }

    }

    /**
     * Refresh token for a specified user.
     *
     * @param App\User $user The user to have the token refreshed.
     *
     * @return bool Whether the token was refreshed or not.
     */
    private function refreshTokenFor($user)
    {
        if ($user->suap_id && $user->suap_key) {
            try {
                $user->refreshToken();
                return true;
            } catch (\Exception $e) {
                $this->error('Could not get a token for user #' . $user->id . ' | Error: ' . $e->getMessage());
            }
        } else {
            $this->error('User #'.$user->id.' does not have SUAP credentials.');
        }
        return false;
    }
}
