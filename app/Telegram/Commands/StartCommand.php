<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;
use App\User;

/**
 * Defines the /start command.
 * Telegram requires all users to send this command before using a bot.
 * Should be used to create a new user, set up stuff and things and etc. :P.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class StartCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'start';

    /**
     * {@inheritdoc}
     */
    const PREFIX = 'start';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Inicia a interação com o bot e mostra o tutorial.';

    /**
     * {@inheritdoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction(['action' => 'typing']);

        $msg = 'Olá '.$this->update['message']['from']['first_name'].'. Eu sou o SUAP Bot, eu posso te mostrar informações sobre suas notas, faltas, locais, materiais e horários de aula, turmas virtuais, e colegas de classe.';
        $this->replyWithMessage(['text' => $msg]);

        $this->replyWithChatAction(['action' => 'typing']);

        $message = $this->update['message'];

        // Get user details.
        $user_id = $message['from']['id'];
        $user_first_name = $message['from']['first_name'];
        $user_last_name = $user_username = null;

        if (isset($message['from']['last_name'])) {
            $user_last_name = $message['from']['last_name'];
        }

        if (isset($message['from']['username'])) {
            $user_username = $message['from']['username'];
        }

        // Create a new user object, or updated it if it already exists.
        $user = User::where('telegram_id', $user_id)->updateOrCreate([
           'telegram_id' => $user_id,
        ], [
           'first_name' => $user_first_name,
           'last_name'  => $user_last_name,
           'username'   => $user_username,
        ]);

        if (!$user->settings) {
            // Create a new settings object for this user.
            $user->settings()->create([
                'grades'     => true,
                'classes'    => true,
                'attendance' => true,
            ]);
        }

        if (!$user->suap_id) {
            // Show the user how to authorize access to suap.
            $this->replyWithMessage([
                'text'       => Speaker::tutorial($user),
                'parse_mode' => 'markdown',
            ]);
        } else {
            // Reply with the commands list.
            $this->replyWithMessage([
                'text'         => "✅ Você já autorizou o acesso ao SUAP. \n\nEnvie um comando como /boletim, /aulas ou /turmas, ou use os botões abaixo.",
                'reply_markup' => Speaker::getReplyKeyboardMarkup(),
            ]);
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
