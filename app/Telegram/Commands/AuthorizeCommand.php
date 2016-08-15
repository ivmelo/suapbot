<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;

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
     * @inheritdoc
     */
    public function handle($arguments)
    {


        $updates = $this->getTelegram()->getWebhookUpdates();

        //$json_updates = json_encode($updates);

        $telegram_id = $updates['message']['from']['id'];

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();

        //$this->replyWithMessage(['text' => $user->toJson()]);


        if ($user) {

            $args = explode(' ', $arguments);

            if (count($args) >= 2) {
                $id = $args[0];
                $key = $args[1];

                if ($user->suap_id && $user->suap_key) {
                    $this->replyWithMessage([
                        'text' => 'Você já autorizou o acesso ao SUAP. Digite /notas para ver suas notas.'//$suap_data_json,
                    ]);
                } else {
                    // use try catch

                    try {
                        $client = new SUAPClient($id, $key, true);

                        $suap_data = $client->getStudentData();

                        if ($suap_data) {
                            $user->suap_id = $id;
                            $user->suap_key = $key;
                            $user->email = $suap_data['email_pessoal'];

                            // get courses data
                            $course_data = $client->getGrades();
                            $course_data_json = json_encode($course_data);
                            $user->course_data = $course_data_json;

                            $user->save();
                        }

                        $suap_data_json = json_encode($suap_data);

                        $name = $suap_data['nome'];
                        $situation = $suap_data['situacao'];
                        $program = $suap_data['curso'];
                        $email = $suap_data['email_pessoal'];

                        //$grades_response = $this->buildTextResponse($suap_data_json);

                        $this->replyWithMessage([
                            'parse_mode' => 'markdown',
                            'text' => 'Massa! Sua conta foi autorizada com sucesso.

*Nome:* ' . $name . '
*Curso:* ' . $program . '
*Situação:* ' . $situation . '

Digite /notas para ver suas notas ou /help para ver uma lista de comandos disponíveis.'//$suap_data_json,
                        ]);
                    } catch (\Exception $e) {
                        $this->replyWithMessage([
                            'text' => 'Ocorreu um erro ao autorizar o seu acesso. Por favor, verifique suas credenciais e tente novamente. Caso precise de ajuda, digite /start e siga o tutorial.'
                        ]);
                    }

                }

            } else {
                $this->replyWithMessage(['text' => 'Por favor, envie suas credenciais no formato: /autorizar <matricula> <chave-de-acesso>. Caso precise de ajuda, digite /start e siga o tutorial.']);
            }

        } else {
            $this->replyWithMessage(['text' => 'Ocorreu um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.']);
        }






    }

}
