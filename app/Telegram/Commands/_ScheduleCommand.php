<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Speaker;
use App\Telegram\Tools\Markify;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class ClassesCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'aulas';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra locais e horários de aula.';

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

        // If the user was found.
        if ($user) {

            // User has set credentials.
            if ($user->suap_id && $user->suap_key) {
                if ($user->suap_token) {

                    $client = new SUAP($user->suap_token);

                    // Get schedule for the requested day of the week.
                    $day = $this->getDayNumber($arguments);
                    $schedule = $client->getHorarios($user->school_year, $user->school_term);

                    // Send schedule to the user.
                    $this->replyWithMessage([
                        'text'       => Markify::parseSchedule($schedule, $day),
                        'parse_mode' => 'markdown',
                        'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                    ]);

                    $user->updateLastRequest();
                    $user->save();

                    try {

                    } catch (\Exception $e) {
                        // Error fetching data from suap.
                        Bugsnag::notifyException($e);
                        $this->replyWithMessage(['text' => Speaker::suapError()]);
                    }
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
     * Converts a possible day of the week (in full or numeric) to a number.
     *
     * @var string Possible day of the week.
     *
     * @return int Int representation of the day of the week.
     */
    private function getDayNumber($day)
    {
        $day = trim(mb_strtolower($day));

        $day_num = date('w') + 1;

        if (str_contains($day, ['1', 'dom', 'sun'])) {
            $day_num =  1;
        } elseif (str_contains($day, ['2', 'seg', 'mon'])) {
            $day_num =  2;
        } elseif (str_contains($day, ['3', 'ter', 'tue'])) {
            $day_num =  3;
        } elseif (str_contains($day, ['4', 'qua', 'wed'])) {
            $day_num =  4;
        } elseif (str_contains($day, ['5', 'qui', 'thr', 'thu'])) {
            $day_num =  5;
        } elseif (str_contains($day, ['6', 'sex', 'fri'])) {
            $day_num =  6;
        } elseif (str_contains($day, ['7', 'sab', 'sat'])) {
            $day_num =  7;
        } elseif (str_contains($day, ['amanhã', 'amanha', 'tomorrow', 'tmr'])) {
            $day_num++;
        }

        if ($day_num > 7) {
            $day_num = 1;
        }

        return $day_num;
    }
}
