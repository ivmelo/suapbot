<?php

namespace App\Telegram\Commands;

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

        // If the user is found.
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
                    ]);

                    $user->updateLastRequest();
                    $user->save();

                    try {

                    } catch (\Exception $e) {
                        // Error fetching data from suap.
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

        switch ($day) {
            case 1:
            case 'domingo':
            case 'sunday':
            case 'sun':
            case 'dom':
                return 1;
                break;

            case 2:
            case 'segunda':
            case 'seg':
            case 'segunda-feira':
            case 'segunda feira':
            case 'monday':
            case 'mon':
                return 2;
                break;

            case 3:
            case 'terça':
            case 'terca':
            case 'ter':
            case 'terça-feira':
            case 'terca-feira':
            case 'terça feira':
            case 'terca feira':
            case 'tuesday':
            case 'tue':
                return 3;
                break;

            case 4:
            case 'quarta':
            case 'qua':
            case 'quarta-feira':
            case 'quarta feira':
            case 'wednesday':
            case 'wed':
            case 'weed':
                return 4;
                break;

            case 5:
            case 'quinta':
            case 'qui':
            case 'quinta-feira':
            case 'quinta feira':
            case 'thrusday':
            case 'thr':
            case 'thu':
                return 5;
                break;

            case 6:
            case 'sexta':
            case 'sex':
            case 'sexta-feira':
            case 'sexta feira':
            case 'friday':
            case 'fri':
                return 6;
                break;

            case 7:
            case 'sábado':
            case 'sabado':
            case 'sáb':
            case 'sab':
            case 'saturday':
            case 'sat':
            case 'caturday':
                return 7;
                break;

            case 'amanhã':
            case 'amanha':
            case 'amn':
            case 'tomorrow':
            case 'tmr':
                $day = date('w') + 2;
                if ($day > 7) {
                    $day = 1;
                }

                return $day;
                break;

            case 'depois de amanhã':
            case 'depois de amanhã':
            case 'depois damanhã':
            case 'depois damanha':
                $day = date('w') + 3;
                if ($day == 9) {
                    $day = 1;
                } elseif ($day == 10) {
                    $day = 2;
                } elseif ($day == 11) {
                    $day = 3;
                }

                return $day;
                break;

            default:
                return date('w') + 1;
                break;
        }
    }
}
