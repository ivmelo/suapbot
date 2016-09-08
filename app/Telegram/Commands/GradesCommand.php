<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;


class GradesCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'notas';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra as suas notas e faltas.';

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
                    // Get grades from SUAP.
                    $client = new SUAPClient($user->suap_id, $user->suap_key, true);

                    // If filtering...
                    if ($arguments) {
                        $grades = $client->filterCoursesByName(trim($arguments));

                        // No filter results.
                        if (empty($grades)) {
                            $this->replyWithMessage([
                                'text' => 'Não foram encontradas disciplinas contendo o(s) termo(s) "' . $arguments . '" no seu boletim.',
                                'parse_mode' => 'markdown'
                            ]);
                        }
                    } else {
                        // No filter. Get all and save to DB.
                        $grades = $client->getGrades();

                        // Store JSON grades data in the DB.
                        $course_data_json = json_encode($grades);
                        $user->course_data = $course_data_json;
                        $user->save();

                        // No grades.
                        if (empty($grades)) {
                            if ($user->notify) {
                                $notify_message = 'Mas fique de olho, te avisarei quando novas disciplinas forem adicionadas lá. 🙂';
                            } else {
                                $notify_message = 'Caso queira receber notificações quando novas disciplinas forem adicionadas, use o comando /notificar.';
                            }

                            $this->replyWithMessage([
                                'text' => 'Não há disciplinas no seu boletim. ' . $notify_message,
                                'parse_mode' => 'markdown'
                            ]);
                        }
                    }

                    // If results, parse grades and display them.
                    if (! empty($grades)) {
                        $grades_response = Markify::parseBoletim($grades);

                        // Send grades to the user.
                        $this->replyWithMessage([
                            'text' => $grades_response,
                            'parse_mode' => 'markdown'
                        ]);
                    }

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
