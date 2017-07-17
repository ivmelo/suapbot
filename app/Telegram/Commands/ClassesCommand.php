<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;
use App\Telegram\Tools\Speaker;
use Ivmelo\SUAP\SUAP;
use App\User;

/**
 * Show classes, class materials, the registered students,
 * class schedules, class details and a bunch of other information.
 * Uses the "Turmas Virtuais" endpoint from the SUAP API.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class ClassesCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'turmas';

    /**
     * {@inheritDoc}
     */
    const ALIASES = [
        'materiais', 'material', 'alunos',
        'colega', 'colegas', 'turma'
    ];

    /**
     * {@inheritDoc}
     */
    const PREFIX = 'classes';

    /**
     * {@inheritDoc}
     */
    const DESCRIPTION = 'Mostra as turmas virtuais incluindo material e alunos.';

    /**
     * {@inheritDoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction(['action' => 'typing',]);

        $user = User::with('settings')->where(
            'telegram_id',
            $this->update['message']['from']['id']
        )->first();

        if ($user) {
            if ($user->suap_token) {
                $suap = new SUAP($user->suap_token);

                try {
                    $turmas = $suap->getTurmasVirtuais($user->school_year, $user->school_term);

                    if (count($turmas) > 0) {
                        $this->replyWithMessage([
                            'text' => "📚 *Turmas Virtuais:* \n\nSelecione uma turma para ver detalhes da turma, materiais de aula, participantes e mais.",
                            'parse_mode' => 'markdown',
                            'reply_markup' => $this->getKeyboard($turmas),
                        ]);
                    } else {
                        $this->replyWithMessage([
                            'text' => "ℹ️ Sem turmas!",
                        ]);
                    }

                } catch (\Exception $e) {
                    $this->replyWithMessage([
                        'parse_mode' => 'markdown',
                        'text' => "⚠️ Houve um erro ao recuperar as suas turmas.",
                    ]);
                }
            } else {
                $this->replyWithMessage(['text' => Speaker::noCredentials($user)]);
            }
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function handleCallback($callback_data)
    {
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
            }
        } catch (\Exception $e) {
            $this->replyWithMessage([
                'text' => "⚠️ Um erro ocorreu. Tente novamente mais tarde..",
            ]);
        }
    }

    /**
     * Shows all the given glasses, with date and info.
     *
     * @param  array $turma The "turma" object.
     */
    private function showAulas($turma)
    {
        $response = "🎒 *Aulas da Disciplina:*\n\n";

        foreach ($turma['aulas'] as $aula) {
            $response .= "📝 " . $aula['conteudo'] . "\n";
            $response .= "📊 " . $aula['quantidade'] . " aulas, " . $aula['faltas'] . " faltas.\n";
            $response .= "📅 " . $this->parseDate($aula['data']) . "\n\n";
        }

        $this->replywithEditedMessage([
            'text'       => $response,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => 'true', // Removes preview for web links.
            'reply_markup' => $this->getNavigationKeyboard($turma, 'aulas')
        ]);
    }

    /**
     * Shows a listing of class materials and URLs to download them.
     *
     * @param  array $turma The "turma" object.
     */
    private function showMateriais($turma)
    {
        $response = "📚 *Materiais de Aula:*\n\n";

        foreach ($turma['materiais_de_aula'] as $material) {
            $response .= "📓 " . $material['descricao'] . "\n";
            $response .= "📅 " . $this->parseDate($material['data_vinculacao']) . "\n";
            $response .= "🗂 " . "[https://suap.ifrn.edu.br" . $material['url'] . "]\n\n";
        }

        $this->replywithEditedMessage([
            'text'       => $response,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => 'true',
            'reply_markup' => $this->getNavigationKeyboard($turma, 'material')
        ]);
    }

    /**
     * Shows the names all students registered in the class, and their contact info.
     *
     * @param  array $turma The "turma" object.
     */
    private function showAlunos($turma)
    {
        $response = "👩‍🎓👨‍🎓 *Alunos*:\n\n";

        foreach ($turma['participantes'] as $participante) {
            $response .= "👨‍🎓 " . $participante['nome'] . "\n";
            $response .= "🎓 " . $participante['matricula'] . "\n";
            $response .= "" . $participante['email'] . "\n\n";
        }

        $this->replywithEditedMessage([
            'text'       => $response,
            'parse_mode' => 'markdown',
            'reply_markup' => $this->getNavigationKeyboard($turma, 'alunos')
        ]);
    }

    /**
     * Shows class details.
     *
     * @param  array $turma The "turma" object.
     */
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
            $response .= "‍🏫 *" . $localdeaula . "*\n";
        }

        $this->replywithEditedMessage([
            'text'       => $response, //Markify::parseBoletim($reportCard),
            'parse_mode' => 'markdown',
            'reply_markup' => $this->getNavigationKeyboard($turma, 'show')
        ]);
    }

    /**
     * Returns a navigation keyboard for class info.
     *
     * @param array $turma The "turma" object.
     * @param string $action The current displayed option to hide.
     * @return \Telegram\Bot\Keyboard\Keyboard $keyboard
     */
    private function getNavigationKeyboard($turma, $action = false)
    {
        $keyboard = Keyboard::make()->inline();

        // Create buttons.
        $aulas_btn = Keyboard::inlineButton([
            'text' => '🎒 Aulas',
            'callback_data' => self::PREFIX . '.' . $turma['id'] . '.aulas',
        ]);

        $material_btn = Keyboard::inlineButton([
            'text' => '📚 Material',
            'callback_data' => self::PREFIX . '.' . $turma['id'] . '.material',
        ]);

        $alunos_btn = Keyboard::inlineButton([
            'text' => '👩‍🎓 Alunos',
            'callback_data' => self::PREFIX . '.' . $turma['id'] . '.alunos',
        ]);

        $turmas_btn = Keyboard::inlineButton([
            'text' => '📖 Turma',
            'callback_data' => self::PREFIX . '.' . $turma['id'] . '.show',
        ]);

        // Create a keyboard without the current displayed option.
        switch ($action) {
            case 'show':
                $keyboard->row($aulas_btn, $material_btn, $alunos_btn);
                break;
            case 'aulas':
                $keyboard->row($material_btn, $alunos_btn, $turmas_btn);
                break;
            case 'material':
                $keyboard->row($aulas_btn, $alunos_btn, $turmas_btn);
                break;
            case 'alunos':
                $keyboard->row($aulas_btn, $material_btn, $turmas_btn);
                break;
            default:
                $keyboard->row($aulas_btn, $material_btn, $alunos_btn, $turmas_btn);
                break;
        }

        return $keyboard;
    }

    /**
     * Returns an inline keyboard with one class per row.
     *
     * @param array $turma The "turma" object.
     * @return \Telegram\Bot\Keyboard\Keyboard $keyboard
     */
    private function getKeyboard($turmas) {

        $keyboard = Keyboard::make()->inline();

        foreach ($turmas as $turma) {
            $keyboard->row(Keyboard::inlineButton([
                'text' => '📖 ' . $turma['descricao'],
                'callback_data' => self::PREFIX . '.' . $turma['id'] . '.show.',
            ]));
        }

        return $keyboard;
    }

    /**
     * Parse a date to DD/MM/YYYY.
     *
     * @param  string $date The date to be parsed.
     * @return string The parsed date.
     */
    private function parseDate($date)
    {
        $arr_date = explode('-', $date);
        $arr_date = array_reverse($arr_date);
        return implode('/', $arr_date);
    }
}
