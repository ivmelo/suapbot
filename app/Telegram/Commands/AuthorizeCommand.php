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
    protected $description = 'Autoriza a sua conta do SUAP. Para usar, digite /autorizar <matricula> <chave_de_acesso>.';

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

                        //$grades_response = $this->buildTextResponse($suap_data_json);

                        $this->replyWithMessage([
                            'text' => 'Autorizado com sucesso. Digite /notas para ver suas notas.'//$suap_data_json,
                        ]);
                    } catch (\Exception $e) {
                        $this->replyWithMessage([
                            'text' => 'Ocorreu um erro ao autorizar o seu acesso. Por favor, verifique suas credenciais e tente novamente.'//$suap_data_json,
                        ]);
                    }

                }

            } else {
                $this->replyWithMessage(['text' => 'Por favor, envie suas credenciais no formato: /autorizar <matricula> <chave>']);
            }

        } else {
            $this->replyWithMessage(['text' => 'Houve um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.']);
        }






    }

}
