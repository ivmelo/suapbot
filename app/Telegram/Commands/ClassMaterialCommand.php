<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;

use Ivmelo\SUAP\SUAP;
use App\User;
use App\Telegram\Tools\Speaker;

/**
 * New Settings Command.
 */
class ClassMaterialCommand extends BotCommand
{
    const NAME = 'turmas';
    const DESCRIPTION = 'Mostra os materiais de aula do usuário.';

    const TURMAS_PREFIX = 'turmas';

    protected function handleCommand()
    {
        $this->replyWithChatAction([
            'action' => 'typing',
        ]);

        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['message']['from']['id']
        )->first();

        if ($user) {
            if ($user) {
                $suap = new SUAP($user->suap_token);
                $turmas = $suap->getTurmasVirtuais($user->school_year, $user->school_term);

                $this->replyWithMessage([
                    'text' => "📚 *Turmas Virtuais:* \n\nSelecione uma turma para ver detalhes da turma, materiais de aula, participantes e mais.",
                    'reply_markup' => $this->getKeyboard($turmas),
                ]);
            }
        }
    }

    protected function handleCallback($callback_data)
    {
        $this->replyWithChatAction([
            'action' => 'typing',
        ]);

        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['callback_query']['from']['id']
        )->first();

        $settings = explode('.', $callback_data);

        $suap = new SUAP($user->suap_token);

        try {
            $turma = $suap->getTurmaVirtual($settings[1]);

            switch ($settings[2]) {
                case 'show':
                    $this->showTurma($turma);
                    break;
                case 'alunos':
                    $this->showAlunos($turma);
                    break;
                case 'material':
                    $this->showMateriais($turma);
                    break;
                case 'aulas':
                    $this->showAulas($turma);
                    break;

                default:
                    # code...
                    break;
            }


        } catch (Exception $e) {

        }

    }

    private function showAulas($turma)
    {
        $response = "🎒 *Aulas da Disciplina:*\n\n";

        foreach ($turma['aulas'] as $aula) {
            $response .= "📝 " . $aula['conteudo'] . "\n";
            $response .= "📊 " . $aula['quantidade'] . " aulas, " . $aula['faltas'] . " faltas.\n";
            $response .= "📅 " . $this->parseDate($aula['data']) . "\n\n";
        }

        $this->replywithMessage([
            'text'       => $response, //Markify::parseBoletim($reportCard),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => 'true',
            'reply_markup' => $this->getNavigationKeyboard($turma)
        ]);
    }

    private function parseDate($date)
    {
        $arr_date = explode('-', $date);
        $arr_date = array_reverse($arr_date);
        return implode('/', $arr_date);
    }

    private function showMateriais($turma)
    {
        $response = "📚 *Materiais de Aula:*\n\n";

        foreach ($turma['materiais_de_aula'] as $material) {
            $response .= "👨‍🎓 " . $material['descricao'] . "\n";
            $response .= "📅 " . $this->parseDate($material['data_vinculacao']) . "\n";
            $response .= "🗂 " . "[https://suap.ifrn.edu.br" . $material['url'] . "]\n\n";
        }

        $this->replywithMessage([
            'text'       => $response, //Markify::parseBoletim($reportCard),
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => 'true',
            'reply_markup' => $this->getNavigationKeyboard($turma)
        ]);
    }

    private function showAlunos($turma)
    {
        $response = "🎓 *Alunos:*\n\n";

        foreach ($turma['participantes'] as $participante) {
            $response .= "👨‍🎓 " . $participante['nome'] . "\n";
            $response .= "🆔 " . $participante['matricula'] . "\n";
            $response .= "" . $participante['email'] . "\n\n";
        }

        $this->replywithMessage([
            'text'       => $response, //Markify::parseBoletim($reportCard),
            'parse_mode' => 'markdown',
            'reply_markup' => $this->getNavigationKeyboard($turma)
        ]);
    }

    private function showTurma($turma)
    {
        $response = '';
        $response .= "📖 *" . $turma['componente_curricular'] . "*\n\n";

        foreach ($turma['professores'] as $professor) {
            $response .= "👨‍🏫 *" . $professor['nome'] . "\n";
            $response .= "‍📧 *" . $professor['email'] . "\n";
        }

        $response .= "\n";

        foreach ($turma['locais_de_aula'] as $localdeaula) {
            $response .= "‍📧 *" . $localdeaula . "*\n";
        }

        $this->replywithMessage([
            'text'       => $response, //Markify::parseBoletim($reportCard),
            'parse_mode' => 'markdown',
            'reply_markup' => $this->getNavigationKeyboard($turma)
        ]);
    }

    private function getNavigationKeyboard($turma)
    {
        $keyboard = Keyboard::make()->inline();

        $keyboard->row(
            Keyboard::inlineButton([
                'text' => 'Aulas',
                'callback_data' => self::TURMAS_PREFIX . '.' . $turma['id'] . '.aulas',
            ]),
            Keyboard::inlineButton([
                'text' => 'Material',
                'callback_data' => self::TURMAS_PREFIX . '.' . $turma['id'] . '.material',
            ])
        );

        $keyboard->row(
            Keyboard::inlineButton([
                'text' => 'Alunos',
                'callback_data' => self::TURMAS_PREFIX . '.' . $turma['id'] . '.alunos',
            ]),
            Keyboard::inlineButton([
                'text' => 'Detalhes',
                'callback_data' => self::TURMAS_PREFIX . '.' . $turma['id'] . '.show',
            ])
        );

        return $keyboard;
    }


    private function getKeyboard($turmas) {

        $keyboard = Keyboard::make()->inline();

        foreach ($turmas as $turma) {
            $keyboard->row(Keyboard::inlineButton([
                'text' => '📖 ' . $turma['descricao'],
                'callback_data' => self::TURMAS_PREFIX . '.' . $turma['id'] . '.show.',
            ]));
        }

        return $keyboard;
    }
}
