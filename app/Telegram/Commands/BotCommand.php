<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * Implements an easy way to handle bot commands.
 */
class BotCommand
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

    protected function replyWithMessage($params)
    {
        $params = $this->prepareParams($params);
        $this->telegram->sendMessage($params);
    }

    protected function replyWithEditedMessage($params)
    {
        $params = $this->prepareParams($params);
        $this->telegram->editMessageText($params);
    }

    protected function replyWithChatAction($params) {
        $params = $this->prepareParams($params);
        $this->telegram->sendChatAction($params);
    }

    private function prepareParams($params) {
        if ($this->update->isType('callback_query')) {
            $params['chat_id'] = $this->update['callback_query']['from']['id'];
            $params['message_id'] = $this->update['callback_query']['message']['message_id'];
        } else {
            $params['chat_id'] = $this->message['chat']['id'];
        }

        $params['parse_mode'] = 'markdown';

        return $params;
    }
}
