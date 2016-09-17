<?php

namespace App\Jobs;

use Telegram;
use App\User;
use App\Jobs\Job;
use App\Telegram\Tools\Markify;
use \Ivmelo\SUAPClient\SUAPClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            $client = new SUAPClient($this->user->suap_id, $this->user->suap_key, true);
            $new_data = $client->getGrades();

            $current_data = json_decode($current_json, true);

            if (count($new_data) != count($current_data)) {
                // One or more courses were added or removed.
                // Notify the user and show their new report card.
                Telegram::sendChatAction([
                    'chat_id' => $this->user->telegram_id,
                    'action' => Telegram\Bot\Actions::TYPING
                ]);

                $message = [
                    'chat_id' => $this->user->telegram_id,
                    'text' => 'Uma ou mais disciplinas foram adicionadas ou removidas do seu boletim. Digite /notas para ver o seu boletim atualizado.',
                    'parse_mode' => 'markdown',
                    'reply_markup' => Speaker::getReplyKeyboardMarkup()
                ];

                Telegram::sendMessage($message);

                // Save report card updates.
                $course_data_json = json_encode($new_data);
                $this->user->course_data = $course_data_json;
                $this->user->save();

                // debug only
                print('#UID: ' . $this->user->id . ' | Courses added/removed. User notified.\n');
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
                        'action' => Telegram\Bot\Actions::TYPING
                    ]);

                    // Parse grades into a readable format.
                    $grades_response = Markify::parseBoletim($updates);

                    $grades_response = "_Boletim Atualizado_\n"
                        . $grades_response . "Digite /notas para ver o boletim completo.";

                    // Send grades to the user.
                    $message = [
                        'chat_id' => $this->user->telegram_id,
                        'text' => $grades_response,
                        'parse_mode' => 'markdown'
                    ];

                    Telegram::sendMessage($message);

                    // Save report card updates.
                    $course_data_json = json_encode($new_data);
                    $this->user->course_data = $course_data_json;
                    $this->user->save();

                    print('#UID: ' . $this->user->id . ' | Report card updated. User notified.\n');

                } else {
                    // Nothing has changed. Do nothing.
                    print('No changes.');
                }
            }

        } catch (\Exception $e) {
            // Error fetching data from SUAP, or parsing report card data.
            print('#UID: ' . $this->user->id . ' | Exception: ' . $e->getMessage());
        }

    }
}
