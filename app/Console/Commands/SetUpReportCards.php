<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Ivmelo\SUAP\SUAP;

class SetUpReportCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suapbot:setupreportcards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up new report card objects for each user.';

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

        foreach ($users as $user) {
            if (!$user->course_data) {
                if ($user->suap_token) {
                    $class_data = [];

                    $suap = new SUAP($user->suap_token);
                    $report_card = $suap->getMeuBoletim($user->school_year, $user->school_term);

                    $user->report_card()->create([
                        'course_data' => json_encode($report_card),
                    ]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
