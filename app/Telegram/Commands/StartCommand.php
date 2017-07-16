<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;
use App\User;

/**
 * Help Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class StartCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'start';

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Inicia a interação com o bot e mostra o tutorial.';

    /**
     * Handles a command call.
     *
     * @param string $message
     */
    protected function handleCommand($message)
    {
        // Type.
        $this->replyWithChatAction(['action' => 'typing']);

        $msg = 'Olá '. $this->update['message']['from']['first_name'] .'. Eu sou o SUAP Bot, eu posso te mostrar informações sobre suas notas, faltas, locais, materiais e horários de aula, turmas virtuais, e colegas de classe.';
        $this->replyWithMessage(['text' => $msg]);

        $this->replyWithChatAction(['action' => 'typing']);

        $message = $this->update['message'];

        $user_id = $message['from']['id'];
        $user_first_name = $message['from']['first_name'];
        $user_last_name = $user_username = null;

        if (isset($message['from']['last_name'])) {
           $user_last_name = $message['from']['last_name'];
        }

        if (isset($message['from']['username'])) {
           $user_username = $message['from']['username'];
        }

        $user = User::where('telegram_id', $user_id)->updateOrCreate([
           'telegram_id' => $user_id
        ], [
           'first_name' => $user_first_name,
           'last_name' => $user_last_name,
           'username' => $user_username,
        ]);

        if (! $user->settings) {
            // Store a new settings object for this user.
            $user->settings()->create([
                'grades' => true,
                'classes' => true,
                'attendance' => true,
            ]);
        }

        if (!$user->suap_id) {
            $this->replyWithMessage([
                'text'       => Speaker::tutorial($user),
                'parse_mode' => 'markdown',
            ]);
        } else {
            // Reply with the commands list
            $this->replyWithMessage([
                'text'         => "✅ Você já autorizou o acesso ao SUAP. \n\nEnvie um comando como /boletim, /aulas ou /turmas, ou use os botões abaixo.",
                'reply_markup' => Speaker::getReplyKeyboardMarkup(),
            ]);
        }
    }

    /**
     * Handles a callback query.
     * This method MUST be implemented, even if it's not used.
     *
     * @param  string $callback_data
     */
    protected function handleCallback($callback_data)
    {
        # This method must be implemented...
        return;
    }
}
