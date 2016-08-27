<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Speaker;

class NotifyCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'notificar';

    /**
     * @var string Command Description
     */
    protected $description = 'Ativa/desativa notificação de atualização de boletim.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {

        $updates = $this->getTelegram()->getWebhookUpdates();
        $telegram_id = $updates['message']['from']['id'];
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $user = User::where('telegram_id', $telegram_id)->first();

        if ($user) {

            // If the user is connected to SUAP.
            if ($user->suap_id && $user->suap_key) {
                if (! $user->notify) {
                    $user->notify = true;
                    $user->save();

                    $this->replyWithMessage([
                        'parse_mode' => 'markdown',
                        'text' => 'As notificações de atualização de boletim foram *ativadas*. Você irá receber notificações quando houver novas faltas, aulas ou notas no seu boletim. Para desativar digite /notificar.'
                    ]);
                } else {
                    $user->notify = false;
                    $user->save();

                    $this->replyWithMessage([
                        'parse_mode' => 'markdown',
                        'text' => 'As notificações de atualização de boletim foram *desativadas*. Você não receberá mais notificações quando houver novas faltas, aulas ou notas no seu boletim. Para reativar digite /notificar.'
                    ]);
                }
            } else {
                $this->replyWithMessage([
                    'parse_mode' => 'markdown',
                    'text' => 'Você precisa autorizar o acesso ao SUAP antes de ativar as notificações. Caso precise de ajuda, digite /start e siga o tutorial.'
                ]);
            }

        } else {
            $this->replyWithMessage(['text' => Speaker::userNotFound()]);
        }
    }

}
