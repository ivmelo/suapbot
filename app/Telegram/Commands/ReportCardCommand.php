<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use View;

/**
 * Shows a student report card with grades, attendance and other info.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class ReportCardCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'boletim';

    /**
     * {@inheritDoc}
     */
    const ALIASES = [
        'notas', 'faltas',
        'presença', 'frequência'
    ];

    /**
     * {@inheritDoc}
     */
    const PREFIX = 'reportcard';

    /**
     * {@inheritDoc}
     */
    const DESCRIPTION = 'Mostra o boletim com notas, aulas e frequência.';

    /**
     * {@inheritDoc}
     */
    protected function handleCommand($message)
    {
        // Connect to telegram and send typing action.
        $this->replyWithChatAction(['action' => 'typing']);

        $telegram_id = $this->update['message']['from']['id'];

        // Get user from DB.
        $user = User::where('telegram_id', $telegram_id)->first();

        // If the user is found.
        if ($user) {

            // User has set credentials.
            if ($user->suap_token) {

                try {
                    $suap = new SUAP($user->suap_token);

                    $reportCard = $suap->getMeuBoletim($user->school_year, $user->school_term);

                    $user->report_card->course_data = json_encode($reportCard);
                    $user->report_card->save();
                    $user->updateLastRequest();
                    $user->save();

                    $view = View::make('telegram.reportcard', [
                        'grades' => $reportCard,
                        'stats' => $this->calculateStats($reportCard)
                    ]);

                    $parsed = $view->render();

                    $this->replyWithMessage([
                        'text'       => $parsed, //Markify::parseBoletim($reportCard),
                        'parse_mode' => 'markdown',
                        'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                    ]);

                } catch (\Exception $e) {
                    // Error fetching data from suap.
                    Bugsnag::notifyException($e);
                    $this->replyWithMessage(['text' => Speaker::suapError()]);
                }
            } else {
                // User has not set SUAP credentials.
                $this->replyWithMessage(['text' => Speaker::noCredentials($user)]);
            }
        } else {
            // User was not found.
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    /**
     * Calculates the total course hours, attendance, classes given
     * and skipped classes of a studen, given their report card.
     *
     * @param  array $reportCard The student's report card.
     * @return array    The calculated stats.
     */
    private function calculateStats($reportCard)
    {
        $totalCargaHoraria = 0;
        $totalAulas = 0;
        $totalFaltas = 0;
        $attendance = 0;

        foreach ($reportCard as $grade) {
            // Add to stats.
            if (isset($grade['carga_horaria'])) {
                # code...
                $totalCargaHoraria += $grade['carga_horaria'];
                $totalAulas += $grade['carga_horaria_cumprida'];
                $totalFaltas += $grade['numero_faltas'];
            }
        }

        if ($totalCargaHoraria != 0) {
            // Calculate total attendance.
            if ($totalFaltas == 0) {
                $attendance = 100;
            } else {
                $attendance = 100 * ($totalAulas - $totalFaltas) / $totalAulas;
            }
        }

        $stats = [
            'total_carga_horaria' => $totalCargaHoraria,
            'total_aulas' => $totalAulas,
            'total_faltas' => $totalFaltas,
            'frequencia' => $attendance,
        ];

        return $stats;
    }

    /**
     * {@inheritDoc}
     */
    protected function handleCallback($callback_data)
    {
        # This method must be implemented...
        return;
    }
}
