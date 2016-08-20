<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;

class ClassesCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'aulas';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra locais e horários de aula.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // Connect to telegram and send typing action.
        $updates = $this->getTelegram()->getWebhookUpdates();
        $telegram_id = $updates['message']['from']['id'];
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // Get user from DB.
        $user = User::where('telegram_id', $telegram_id)->first();

        // If the user is found.
        if ($user) {

            // User has set credentials.
            if ($user->suap_id && $user->suap_key) {
                try {
                    // Get schedule from SUAP.
                    $client = new SUAPClient($user->suap_id, $user->suap_key, true);
                    $grades = $client->getSchedule();

                    // Parse grades into a readable format.
                    //$grades_response = Markify::parseBoletim($grades);

                    $grades_response = json_encode($grades);

                    // Send grades to the user.
                    $this->replyWithMessage([
                        'text' => $grades_response,
                        'parse_mode' => 'markdown'
                    ]);

                    // Store JSON grades data in the DB.
                    //$course_data_json = json_encode($grades);
                    //$user->course_data = $course_data_json;
                    $user->save();
                } catch (\Exception $e) {
                    // Error fetching data from suap.
                    $this->replyWithMessage(['text' => Speaker::suapError()]);
                }

            } else {
                // User has not set SUAP credentials.
                $this->replyWithMessage(['text' => Speaker::noCredentials()]);
            }

        } else {
            // User was not found.
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

}
