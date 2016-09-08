<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use App\Telegram\Tools\Speaker;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'start';

    /**
     * @var string Command Description
     */
    protected $description = 'Inicia a interação com o bot e mostra tutorial.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        $updates = $this->getTelegram()->getWebhookUpdates();
        $telegram_id = $updates['message']['from']['id'];
        $first_name = $updates['message']['from']['first_name'];

        $message = 'Olá, ' . $first_name . '! Eu sou o SUAP Bot, eu posso te mostrar informações sobre suas notas e faltas.'; //Se você quiser, também posso te enviar notificações quando novas notas ou faltas forem lançadas (em breve).

        $this->replyWithMessage(['text' => $message]);

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // Get user
        $user = User::where('telegram_id', $telegram_id)->first();

        // User not found. It's their first access.
        if (! $user) {
            // Grab data from telegram and save.
            $user = new User();

            $user->first_name = $first_name;
            $user->telegram_id = $telegram_id;

            if (isset($updates['message']['from']['last_name'])) {
                $user->last_name = $updates['message']['from']['last_name'];
            }

            if (isset($updates['message']['from']['username'])) {
                $user->username = $updates['message']['from']['username'];
            }

            $user->save();
        }

        // This will prepare a list of available commands and send the user.
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);

        if (! $user->suap_id) {
            $this->replyWithMessage([
                'text' => Speaker::tutorial(),
                'parse_mode' => 'markdown',
                'reply_markup' => ['force_reply']
            ]);
        }

    }
}
