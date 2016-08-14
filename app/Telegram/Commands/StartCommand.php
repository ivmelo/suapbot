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
    protected $description = 'Realiza a autenticação no SUAP.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        $updates = $this->getTelegram()->getWebhookUpdates();

        $json_updates = json_encode($updates);

        $telegram_id = $updates['message']['from']['id'];
        $first_name = $updates['message']['from']['first_name'];

        $message = 'Olá, ' . $first_name . '! Eu sou o SUAP Bot, eu posso te mostrar informações sobre suas notas e faltas. Se você quiser, também posso te enviar notificações quando novas notas ou faltas forem lançadas.';


        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
        $this->replyWithMessage(['text' => $message]);

        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();

        if (! $user) {
            $user = new User();

            $user->first_name = $first_name;
            $user->telegram_id = $telegram_id;

            if (array_key_exists('last_name', $updates['message']['from'])) {
                $user->last_name = $updates['message']['from']['last_name'];
            }

            if (array_key_exists('username', $updates['message']['from'])) {
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
            //$message = 'Primeiro, preciso do seu IFRN ID para que eu possa conectar-me ao SUAP e pegar as informações do seu curso. Se você é um aluno, o seu IFRN ID é o seu número de matricula. Digite-o agora.';
            $message = 'Primeiro, você precisa me dar acesso aos seus dados do SUAP. Não se preocupe, você só tem que fazer isso uma vez. Para isso, preciso da sua matrícula e da sua chave de acesso, que pode ser encontrada na seção "Meus Dados" do SUAP, na aba "Dados Pessoais". Quando você estiver com o seu chave de acesso, use o comando /autorizar <matricula> <chave_de_acesso> para autenticar. A sua chave de acesso é somente leitura e não será utilizada apenas para acessar o seu boletim quando você solicitar.';


            $this->replyWithMessage([
                'text' => $message,
                'reply_markup' => ['force_reply']
            ]);
        }

        //$this->replyWithMessage(['text' => $json_updates]);

    }
}
