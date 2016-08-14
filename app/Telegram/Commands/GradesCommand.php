<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;

class GradesCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'notas';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra as suas notas lançadas até o momento.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        //$args = explode(' ', $arguments);

        $updates = $this->getTelegram()->getWebhookUpdates();

        $telegram_id = $updates['message']['from']['id'];

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();


        if ($user) {

            if ($user->suap_id && $user->suap_key) {
                $client = new SUAPClient($user->suap_id, $user->suap_key, true);

                $grades = $client->getGrades();

                //$grades_json = json_encode($grades);

                // $filtered_grades = [];
                //
                // if (count($args) > 0) {
                //     foreach ($grades as $grade) {
                //         foreach ($args as $arg) {
                //             if (strpos($grade['disciplina'], $arg) !== false) {
                //                 array_push($filtered_grades, $grade);
                //                 continue;
                //             }
                //         }
                //     }
                // }
                //
                // if (count($filtered_grades) > 0) {
                //     $grades = $filtered_grades;
                // }

                $grades_response = $this->buildTextResponse($grades);

                $this->replyWithMessage([
                    'text' => $grades_response,
                    'parse_mode' => 'markdown'
                ]);

                $course_data_json = json_encode($grades);
                $user->course_data = $course_data_json;

                $user->save();
            } else {
                $this->replyWithMessage(['text' => 'Você ainda não autorizou o acesso ao SUAP. Por favor, digite /autorizar <suap_id> <chave_de_acesso> e tente novamente.']);
            }

        } else {
            $this->replyWithMessage(['text' => 'Houve um erro ao recuperar suas credenciais de acesso. Por favor, digite /start e tente novamente.']);
        }

    }

    private function buildTextResponse($grades) {
        $response_text = '';

        foreach ($grades as $grade) {
            # code...
            $course_info = '*' .$grade['disciplina'] . '*
' . 'Aulas: ' . $grade['aulas'] . '
Faltas:  ' . $grade['faltas'] . ' ';

            if ($grade['situacao'] != 'cursando') {
                $course_info = $course_info . '
Situação: ' . ucfirst($grade['situacao']) . ' ';
            }

            if ($grade['frequencia']) {
                $course_info = $course_info . '
Frequência: ' . $grade['frequencia'] . '% ';
            }

            if ($grade['bm1_nota']) {
                $course_info = $course_info . '
N1: ' . $grade['bm1_nota'] . ' ';
            }

            if ($grade['bm2_nota']) {
                $course_info = $course_info . '
N2: ' . $grade['bm2_nota'] . ' ';
            }

            if ($grade['media']) {
                $course_info = $course_info . '
Média: ' . $grade['media'] . ' ';
            }

            if ($grade['naf_nota']) {
                $course_info = $course_info . '
NAF: ' . $grade['naf_nota'] . ' ';
            }

            if ($grade['mfd']) {
                $course_info = $course_info . '
NAF: ' . $grade['mfd'];
            }

            $course_info = $course_info . '

';

            $response_text =  $response_text . $course_info;
        }

        return $response_text;
    }
}
