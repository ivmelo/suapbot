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
    protected $signature = 'monitor:updateboletim {user_id?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a user report card without notifying the user.';

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
            $users = User::all();

            if ($users->count() < 1) {
                $this->info('No users.');
            } else {
                // Create a progress bar for beautiful display.
                $bar = $this->output->createProgressBar(count($users));

                // Iterate and update.
                foreach ($users as $user) {
                    $this->updateReportCard($user);

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
                $this->updateReportCard($user);
            } else {
                $this->error('User not found!');
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @param App\User $user the user whose report card will be updated.
     */
    private function updateReportCard($user)
    {
        if ($user->suap_id && $user->suap_key) {

            // Current data from database.
            $current_json = $user->course_data;

            try {
                // Get grades from SUAP.
                $client = new SUAP($user->suap_id, $user->suap_key, true);
                $grades = $client->getGrades();

                $current_data = json_decode($current_json, true);

                // no changes. finish loop.
                if ($current_data == $grades) {
                    return;
                }

                // deals with the case in which a report card
                // that was previously filled comes out empty
                if (isset($grades['data']) && !empty($grades['data'])) {
                    $new_data = $grades['data'];
                } else {
                    $new_data = $grades;
                }

                // addapts for new format of data.
                // during the first run will verify
                // and get new data from suap without notifying the user
                if (!isset($current_data['data'])) {
                    $course_data_json = json_encode($grades);
                    $user->course_data = $course_data_json;
                    $user->save();
                } else {
                    // Already using the new format.
                    $current_data = $current_data['data'];
                }

                if (count($new_data) != count($current_data)) {
                    // One or more courses were added or removed.

                    // Save report card updates.
                    $course_data_json = json_encode($grades);
                    $user->course_data = $course_data_json;
                    $user->save();

                    // debug only
                    $this->info('#UID: '.$user->id." | Courses added/removed.\n");
                } else {
                    $updates = [];

                    // Compare course data.
                    for ($i = 0; $i < count($current_data); $i++) {
                        // Grab data for current course.
                        $current_course_data = $current_data[$i];
                        $new_course_data = $new_data[$i];

                        // Compare the old course data with the new course data.
                        if ($updated_data = array_diff_assoc($new_course_data, $current_course_data)) {
                            // Add the course name to the list of updated info, so it can be displayed.
                            $updated_data['disciplina'] = $current_course_data['disciplina'];
                            array_push($updates, $updated_data);
                        }
                    }

                    // If there was an update
                    if (count($updates) > 0) {
                        // Handle report card update.

                        $updated_data = $grades;

                        $updated_data['data'] = $updates;

                        // Parse grades into a readable format.
                        $grades_response = Markify::parseBoletim($updated_data);

                        $grades_response = "*📚 BOLETIM ATUALIZADO*\n\n"
                        .$grades_response.'Digite /notas para ver o boletim completo.';

                        // Save report card updates.
                        $course_data_json = json_encode($grades);
                        $user->course_data = $course_data_json;
                        $user->save();

                        $this->info('#UID: '.$user->id." | Report card updated.\n");
                    } else {
                        // Nothing has changed. Do nothing.
                        $this->info('#UID: '.$user->id." | No changes.\n");
                    }
                }
            } catch (\Exception $e) {
                // Error fetching data from SUAP, or parsing report card data.
                $this->error('#UID: '.$user->id.' | Exception: '.$e->getMessage()."\n");
            }
        } else {
            $this->info('#UID: '.$user->id.' | No SUAP credentials.'."\n");
        }
    }
}
