<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class AuthorizeCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'autorizar';

    /**
     * @var string Command Description
     */
    protected $description = 'Autoriza o acesso a sua conta do SUAP.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $updates = $this->getTelegram()->getWebhookUpdates();
        $telegram_id = $updates['message']['from']['id'];
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();

        if ($user) {
            // Get arguments (matricula and access_key).
            $args = explode(' ', $arguments);

            // Verifies if both arguments were supplied.
            if (count($args) >= 2) {
                $id = $args[0];
                $key = $args[1];

                // Verifies if the user already has SUAP credentials.
                if ($user->suap_id && $user->suap_key) {
                    $this->replyWithMessage([
                        'text' => 'Você já autorizou o acesso ao SUAP. Digite /notas para ver suas notas ou /help para ver uma lista de comandos disponíveis.',
                    ]);
                } else {

                    // Validate SUAP credentials.
                    try {
                        $user->authorize($id, $key);
                    } catch (\Exception $e) {
                        // Authorization error.
                        Bugsnag::notifyException($e);
                        $this->replyWithMessage(['text' => Speaker::authorizationError()]);
                    }
                }
            } else {
                $this->replyWithMessage(['text' => Speaker::authorizationCredentialsMissing()]);
            }
        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }
}
