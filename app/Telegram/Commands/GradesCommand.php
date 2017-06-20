<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use View;

class GradesCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'boletim';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra as suas notas e faltas.';

    /**
     * {@inheritdoc}
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
                    $suap = new SUAP($user->suap_token);

                    $reportCard = $suap->getMeuBoletim($user->school_year, $user->school_term);

                    $user->course_data = json_encode($reportCard);
                    $user->updateLastRequest();
                    $user->save();

                    $view = View::make('telegram.reportcard', [
                        'grades' => $reportCard,
                        'stats' => $this->generateStats($reportCard)
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

    public function generateStats($grades)
    {
        $totalCargaHoraria = 0;
        $totalAulas = 0;
        $totalFaltas = 0;
        $attendance = 0;

        foreach ($grades as $grade) {
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
}
