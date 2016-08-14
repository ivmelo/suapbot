<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Markify;

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
                    $grades = $client->getGrades();

                    // Parse grades into a readable format.
                    $grades_response = Markify::parseBoletim($grades);

                    // Send grades to the user.
                    $this->replyWithMessage([
                        'text' => $grades_response,
                        'parse_mode' => 'markdown'
                    ]);

                    // Store JSON grades data in the DB.
                    $course_data_json = json_encode($grades);
                    $user->course_data = $course_data_json;
                    $user->save();
                } catch (\Exception $e) {
                    // Error fetching data from suap.
                    $this->replyWithMessage(['text' => 'Houve um erro ao conectar-se ao SUAP. Por favor, verifique se o SUAP está online e tente novamente mais tarde.']);
                }

            } else {
                // User has not set SUAP credentials.
                $this->replyWithMessage(['text' => 'Você ainda não autorizou o acesso ao SUAP. Por favor, digite /autorizar <suap_id> <chave_de_acesso> e tente novamente.']);
            }

        } else {
            // User was not found.
            $this->replyWithMessage(['text' => 'Houve um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.']);
        }
    }

}
