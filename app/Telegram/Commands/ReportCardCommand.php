<?php

namespace App\Telegram\Commands;

use App\ReportCard;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Bugsnag;
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
     * {@inheritdoc}
     */
    const NAME = 'boletim';

    /**
     * {@inheritdoc}
     */
    const ALIASES = [
        'notas', 'faltas',
        'presença', 'frequência',
    ];

    /**
     * {@inheritdoc}
     */
    const PREFIX = 'reportcard';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Mostra o boletim com notas, aulas e frequência.';

    /**
     * {@inheritdoc}
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

                    $reportCard = View::make('telegram.reportcard', [
                        'grades' => $reportCard,
                        'stats'  => reportCard::calculateStats($reportCard),
                        'update' => false,
                    ])->render();

                    $this->replyWithMessage([
                        'text'         => $reportCard, //Markify::parseBoletim($reportCard),
                        'parse_mode'   => 'markdown',
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
     * {@inheritdoc}
     */
    protected function handleCallback($callback_data)
    {
        // This method must be implemented...
    }
}
