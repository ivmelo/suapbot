<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * New Settings Command.
 */
class NewSettingsCommand
{
    const NAME = '';

    const DESCRIPTION = '';

    protected $update;

    protected $arguments;

    protected $telegram;

    protected $message;

    public function __construct($telegram, $update)
    {
        $this->update = $update;
        $this->telegram = $telegram;
        $this->message = $update->getMessage();
    }

    public function handle()
    {
        if ($this->update->isType('callback_query')) {
            $this->handleCallback($this->update['callback_query']['data']);
        } else {
            $this->handleCommand();
        }
    }

    protected function handleCommand()
    {
        $keyboard = $this->getKeyboard();

        $this->replyWithMessage([
            'text' => json_encode($this->message),
            'reply_markup' => $keyboard,
        ]);
    }

    protected function handleCallback($callback_data)
    {
        $this->replyWithEditedMessage([
            'text' => $callback_data,
        ]);
    }

    private function getKeyboard() {
        return Keyboard::make()
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
    }

    protected function replyWithMessage($params)
    {
        if ($this->update->isType('callback_query')) {
            $params['chat_id'] = $this->update['callback_query']['from']['id'];
        } else {
            $params['chat_id'] = $this->message['chat']['id'];
        }
        $this->telegram->sendMessage($params);
    }

    protected function replyWithEditedMessage($params)
    {
        if ($this->update->isType('callback_query')) {
            $params['chat_id'] = $this->update['callback_query']['from']['id'];
            $params['message_id'] = $this->update['callback_query']['message']['message_id'];
        } else {
            $params['chat_id'] = $this->message['chat']['id'];
        }

        $this->telegram->editMessageText($params);
    }
}
