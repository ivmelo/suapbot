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
    protected $signature = 'refresh:suaptokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get new SUAP access tokens for one or more users.';

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

        $bar = $this->output->createProgressBar(count($users));

        $this->info('Refreshing all user tokens...');

        foreach ($users as $user) {

            if ($user->suap_id && $user->suap_key) {
                try {
                    $user->refreshToken();
                } catch (\Exception $e) {
                    $this->error('Could not refresh token for user #' . $user->id . ' | Error: ' . $e->getMessage());
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
