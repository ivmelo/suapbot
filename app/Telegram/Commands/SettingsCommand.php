<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Speaker;
use App\Telegram\Tools\Markify;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Foundation\Inspiring;

class SettingsCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'config';

    /**
     * @var string Command Description
     */
    protected $description = 'Mostra configurações de notificação.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        // Connect to telegram and send typing action.
        $update = $this->getUpdate();

        // if ($update->isType('callback_query')) {
            // $query = $update->getCallbackQuery();
            // $data = $query->getData();

            // $this->editMessageText([
            //     'text' => 'Hello World!',
            //     'parse_mode' => 'markdown',
            //     // 'reply_markup' => $keyboard,
            // ]);

            // $this->replyWithMessage([
            //     'text' => 'It works',
            //     'parse_mode' => 'markdown',
            //     // 'reply_markup' => $keyboard,
            // ]);

        // } else {
            // $update = $this->getTelegram()->getWebhookUpdates();

            $telegram_id = $update['message']['from']['id'];
            $this->replyWithChatAction(['action' => Actions::TYPING]);

            // Get user from DB.
            $user = User::where('telegram_id', $telegram_id)->first();

            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton([
                        'text' => '✅ Novas Aulas',
                        'callback_data' => 'aulas.toggle',
                    ])
                )->row(Keyboard::inlineButton([
                    'text' => '✅ Novas Notas',
                    'callback_data' => 'aulas.toggle',
                ]))->row(Keyboard::inlineButton([
                    'text' => '✅ Novas Faltas',
                    'callback_data' => 'skippedclasses.toggle',
                ]));

            $this->replyWithMessage([
                'text' => "🔧 *Configuração de Notificação:* \n\nUse os botões abaixo para definir sobre quais eventos você deseja ser notificado.",
                'parse_mode' => 'markdown',
                'reply_markup' => $keyboard,
            ]);
        // }

    }
}
