<?php

namespace App\Jobs;

use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Bugsnag;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ivmelo\SUAP\SUAP;
use Telegram;

/*
 * This Job monitors if the user's report card (boletim) has changes
 * and notifies the user in case there is.
 */
class MonitorReportCardChanges extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        // Get user.
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Current data from database.
        $current_json = $this->user->course_data;

        try {
            // Get grades from SUAP.
            $client = new SUAP($this->user->suap_id, $this->user->suap_key, true);
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
                $this->user->course_data = $course_data_json;
                $this->user->save();
            } else {
                // Already using the new format.
                $current_data = $current_data['data'];
            }

            if (count($new_data) != count($current_data)) {
                // One or more courses were added or removed.
                // Notify the user and show their new report card.
                Telegram::sendChatAction([
                    'chat_id' => $this->user->telegram_id,
                    'action'  => Telegram\Bot\Actions::TYPING,
                ]);

                $message = [
                    'chat_id'      => $this->user->telegram_id,
                    'text'         => 'Uma ou mais disciplinas foram adicionadas ou removidas do seu boletim. Digite /notas para ver o seu boletim atualizado.',
                    'parse_mode'   => 'markdown',
                    'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                ];

                Telegram::sendMessage($message);

                // Save report card updates.
                $course_data_json = json_encode($grades);
                $this->user->course_data = $course_data_json;
                $this->user->save();

                // debug only
                echo '#UID: '.$this->user->id." | Courses added/removed. User notified.\n";
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

                    Telegram::sendChatAction([
                        'chat_id' => $this->user->telegram_id,
                        'action'  => Telegram\Bot\Actions::TYPING,
                    ]);

                    $updated_data = $grades;

                    $updated_data['data'] = $updates;

                    // Parse grades into a readable format.
                    $grades_response = Markify::parseBoletim($updated_data);

                    $grades_response = "*📚 BOLETIM ATUALIZADO*\n\n"
                        .$grades_response.'Digite /notas para ver o boletim completo.';

                    // Send updates to the user.
                    $message = [
                        'chat_id'      => $this->user->telegram_id,
                        'text'         => $grades_response,
                        'parse_mode'   => 'markdown',
                        'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                    ];

                    Telegram::sendMessage($message);

                    // Save report card updates.
                    $course_data_json = json_encode($grades);
                    $this->user->course_data = $course_data_json;
                    $this->user->save();

                    echo '#UID: '.$this->user->id." | Report card updated. User notified.\n";
                } else {
                    // Nothing has changed. Do nothing.
                    echo '#UID: '.$this->user->id." | No changes.\n";
                }
            }
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            // Error fetching data from SUAP, or parsing report card data.
            echo '#UID: '.$this->user->id.' | Exception: '.$e."\n";
        }
    }
}
