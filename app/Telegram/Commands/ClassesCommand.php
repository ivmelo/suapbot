<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;

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
     * @inheritdoc
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
                    // Get schedule from SUAP.
                    $client = new SUAPClient($user->suap_id, $user->suap_key, true);

                    // Get schedule for the requested day of the week.
                    $day = $this->getDayNumber($arguments);
                    $schedule = $client->getSchedule($day, env('CURRENT_TERM_SUPERIOR'));

                    // Schedule empty, might be a technical student...
                    if (empty($schedule)) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $schedule = $client->getSchedule($day, env('CURRENT_TERM_TECNICO'));
                    }

                    // Choose the appropriate message.
                    if ($this->isToday($day)) {
                        $schedule_response = "*Suas aulas de hoje são:*\n\n";
                    } else {
                        $schedule_response = "*Aulas d" . Speaker::getDayOfTheWeek($day, true) . ":*\n\n";
                    }

                    $has_classes = false;

                    // Format response message.
                    foreach ($schedule as $shift => $hours) {
                        foreach ($hours as $time => $class) {
                            if ($class) {
                                $has_classes = true;
                                $schedule_response .= "*" . $time . ":* \n";
                                $schedule_response .= $class['disciplina'] . "\n_" . $class['local'] . "_\n\n";
                            }
                        }
                    }

                    if (! $has_classes) {
                        if ($this->isToday($day)) {
                            // No classes today.
                            $schedule_response = "Sem aulas hoje. 😃 \nPara ver aulas de outros dias, digite /aulas <dia-da-semana>.";

                        } else {
                            // No classes for the requested day.
                            $schedule_response = "Você não tem aulas no dia socitado. \nPara ver aulas de outros dias, digite /aulas <dia-da-semana>.";
                        }
                    } else {
                        $schedule_response .= "Para ver aulas de outros dias, digite /aulas <dia-da-semana>.";
                    }

                    // Send schedule to the user.
                    $this->replyWithMessage([
                        'text' => $schedule_response,
                        'parse_mode' => 'markdown'
                    ]);

                    $user->save();
                } catch (\Exception $e) {
                    $this->replyWithMessage([
                        'text' => $e->getMessage(),
                        'parse_mode' => 'markdown'
                    ]);

                    // Error fetching data from suap.
                    $this->replyWithMessage(['text' => Speaker::suapError()]);
                }

            } else {
                // User has not set SUAP credentials.
                $this->replyWithMessage(['text' => Speaker::noCredentials()]);
            }

        } else {
            // User was not found.
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

    /**
     * Returns wether the informed day is today or not.
     *
     * @var     int Day of the week.
     *
     * @return  boolean Wether it's today or not.
     */
    private function isToday($day) {
        return $day == date('w') + 1;
    }

    /**
     * Converts a possible day of the week (in full or numeric) to a number.
     *
     * @var     string Possible day of the week.
     *
     * @return  int    Int representation of the day of the week.
     */
    private function getDayNumber($day) {
        switch ($day) {
            case 1:
            case 'domingo':
            case 'sunday':
                return 1;
                break;

            case 2:
            case 'segunda':
            case 'segunda-feira':
            case 'monday':
                return 2;
                break;

            case 3:
            case 'terça':
            case 'terca':
            case 'terça-feira':
            case 'tuesday':
                return 3;
                break;

            case 4:
            case 'quarta':
            case 'quarta-feira':
            case 'wednesday':
                return 4;
                break;

            case 5:
            case 'quinta':
            case 'quinta-feira':
            case 'thrusday':
                return 5;
                break;

            case 6:
            case 'sexta':
            case 'sexta-feira':
            case 'friday':
                return 6;
                break;

            case 7:
            case 'sábado':
            case 'sabado':
            case 'saturday':
                return 7;
                break;

            case 'amanhã':
            case 'amanha':
            case 'tomorrow':
                $day = date('w') + 2;
                if ($day > 8)
                    $day = 1;
                return $day;
                break;

            default:
                return date('w') + 1;
                break;
        }
    }

}
