<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

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

        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
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
        // First, Get an array of all registered commands
        // They'll be in 'command-name' => 'Command Handler Class' format.
        $commands = $this->getTelegram()->getCommands();

        // Build the list
        $response = '';
        foreach ($commands as $name => $command) {
            $response .= sprintf('/%s - %s' . PHP_EOL, $name, $command->getDescription());
        }

        // Reply with the commands list
        $this->replyWithMessage(['text' => $response]);

        // Trigger another command dynamically from within this command
        // When you want to chain multiple commands within one or process the request further.
        // The method supports second parameter arguments which you can optionally pass, By default
        // it'll pass the same arguments that are received for this command originally.
        //$this->triggerCommand('subscribe');

        if (! $user->suap_id) {
            $message = $this->getTutorialMessage();

            $this->replyWithMessage([
                'text' => $message,
                'parse_mode' => 'markdown',
                'reply_markup' => ['force_reply']
            ]);
        }

    }

    private function getTutorialMessage() {
        return 'Primeiro, preciso de autorização para acessar o seu boletim no SUAP.

Para isso, preciso da sua matrícula e da chave de acesso *(não confundir com senha do SUAP)*. A chave de acesso é *somente leitura* e não permite alterar no seus dados no SUAP.

Para pegar a sua chave de acesso siga os seguintes passos:

1 - Faça login no SUAP. https://suap.ifrn.edu.br;
2 - Clique em “Meus Dados”;
3 - Acesse a aba “Dados Pessoais”;
4 - Na ultima linha da tabela de “Dados Gerais” procure pela “Chave de Acesso” (Vai ser algo parecido com 5e8h9);
5 - Copie ou anote a sua chave de acesso.

Pronto! Agora basta digitar:

/autorizar <sua-matricula> <chave-de-acesso>';
    }
}
