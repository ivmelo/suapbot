<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AuthorizeCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'autorizar';

    /**
     * @var string Command Description
     */
    protected $description = 'Autoriza o acesso a sua conta do SUAP.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $updates = $this->getTelegram()->getWebhookUpdates();
        $telegram_id = $updates['message']['from']['id'];
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();

        if ($user) {
            // Get arguments (matricula and access_key).
            $args = explode(' ', $arguments);

            // Verifies if both arguments were supplied.
            if (count($args) >= 2) {
                $id = $args[0];
                $key = $args[1];

                // Verifies if the user already has SUAP credentials.
                if ($user->suap_id && $user->suap_key) {
                    $this->replyWithMessage([
                        'text' => 'Você já autorizou o acesso ao SUAP. Digite /notas para ver suas notas ou /help para ver uma lista de comandos disponíveis.',
                    ]);
                } else {

                    // Validate SUAP credentials.
                    try {
                        $client = new SUAP($id, $key, true);
                        $suap_data = $client->getStudentData();

                        // Save user credentials and Email.
                        if ($suap_data) {
                            $user->suap_id = $id;
                            $user->suap_key = $key;
                            $user->email = $suap_data['email_pessoal'];

                            // Get course data for the first access.
                            $course_data = $client->getGrades();
                            $course_data_json = json_encode($course_data);
                            $user->course_data = $course_data_json;

                            $user->save();
                        }

                        // Grab user info for display.
                        $name = $suap_data['nome'];
                        $program = $suap_data['curso'];
                        $situation = $suap_data['situacao'];

                        // All set, message user.
                        // And set up keyboard.
                        $this->replyWithMessage([
                            'parse_mode'   => 'markdown',
                            'text'         => Speaker::authorized($name, $program, $situation),
                            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                        ]);

                        // Activate notifications.
                        $this->triggerCommand('notificar');
                    } catch (\Exception $e) {
                        // Authorization error.
                        $this->replyWithMessage(['text' => Speaker::authorizationError()]);
                    }
                }
            } else {
                $this->replyWithMessage(['text' => Speaker::authorizationCredentialsMissing()]);
            }
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }
}
