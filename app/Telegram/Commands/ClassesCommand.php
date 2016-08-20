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
                    $schedule = $client->getSchedule($day);

                    // Titles for class schedule...
                    $titles = [
                        "Opa, aqui estão suas aulas de hoje:\n\n",
                        "Estas são as suas aulas de hoje:\n\n",
                        "Alguém disse aulas? As de hoje são:\n\n",
                        "Não vá se atrasar, hein...\n\n",
                        "Toma aê, campeão...\n\n",
                    ];

                    // Chose a random message from the list.
                    $schedule_response = $titles[random_int(0, count($titles) - 1)];

                    // Format response message.
                    foreach ($schedule as $shift => $hours) {
                        foreach ($hours as $time => $class) {
                            if ($class) {
                                $schedule_response .= "*" . $time . ":* \n";
                                $schedule_response .= $class['disciplina'] . "\n_" . $class['local'] . "_\n\n";
                            }
                        }
                    }

                    // Send schedule to the user.
                    $this->replyWithMessage([
                        'text' => $schedule_response,
                        'parse_mode' => 'markdown'
                    ]);

                    $user->save();
                } catch (\Exception $e) {
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
     * Converts a possible day of the week (in full or numeric) to a number.
     *
     * @var     string Possible day of the week.
     *
     * @return  int    Int representation of the day of the week.
     */
    private function getDayNumber($day) {
        switch ($day) {
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

            case 1:
            case 'domingo':
            case 'sunday':
                return 1;
                break;

            default:
                // 'hoje' || 'today' || ''
                return date('w') + 1;
                break;
        }
    }

}
