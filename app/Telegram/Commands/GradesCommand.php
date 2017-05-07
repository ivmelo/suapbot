<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

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

                    // $data = $suap->autenticar($user->suap_id, $user->suap_key, true);
                    // $user->suap_token = $data['token'];
                    // $user->save();

                    $reportCard = $suap->getMeuBoletim($user->school_year, $user->school_term);
                    // $reportCard = $suap->getMeuBoletim(2016, 1);


                    $user->course_data = json_encode($reportCard);
                    $user->updateLastRequest();
                    $user->save();

                    $this->replyWithMessage([
                        'text'       => Markify::parseBoletim($reportCard),
                        'parse_mode' => 'markdown',
                    ]);

                } catch (\Exception $e) {
                    // Error fetching data from suap.
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
}
