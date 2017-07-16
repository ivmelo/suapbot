<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Keyboard\Keyboard;

/**
 * Implements an easy way to handle bot commands.
 */
abstract class Command
{
    /**
     * The name of the command.
     * @var string
     */
    const NAME = '';

    /**
     * Stores the description of the command.
     * @var string
     */
    const DESCRIPTION = '';

    /**
     * The prefix for callback queries.
     * @var string
     */
    const PREFIX = '';

    /**
     * The Telegram update object.
     * @var \Telegram\Bot\Objects\Update $update
     */
    protected $update;

    /**
     * The arguments of the command.
     * @var Array
     */
    protected $arguments;

    /**
     * The Telegram Bot API client.
     * @var \Telegram\Bot\Api $telegram
     */
    protected $telegram;

    /**
     * The message sent by the user.
     * @var string
     */
    protected $message;

    /**
     * The user that received the command.
     * @var string
     */
    protected $user;

    /**
     * The constructor for this class.
     *
     * @param \Telegram\Bot\API $telegram
     * @param \Telegram\Bot\Objects\update $update
     */
    public function __construct($telegram, $update, $user = null)
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->message = $update->getMessage();
        if ($user) {
            $this->user = $user;
        }
    }

    /**
     * Handles a command sent by the user.
     *
     * @param string $message
     */
    abstract protected function handleCommand($message);

    /**
     * Handles a callback_query sent by the user.
     *
     * @param string $callback_data
     */
    abstract protected function handleCallback($callback_data);

    /**
     * Handles the command.
     */
    public function handle()
    {
        if ($this->update->isType('callback_query')) {
            $this->handleCallback($this->update['callback_query']['data']);
        } else {
            $this->handleCommand($this->message);
        }
    }

    /**
     * Replies to the user with a message.
     *
     * @param  Array $params The params for the message.
     */
    protected function replyWithMessage($params)
    {
        $params = $this->prepareParams($params);
        $this->telegram->sendMessage($params);
    }

    /**
     * Replies with an edited message.
     *
     * @param  Array $params The params for the message.
     */
    protected function replyWithEditedMessage($params)
    {
        $params = $this->prepareParams($params);
        $this->telegram->editMessageText($params);
    }

    /**
     * Replies with a chat action.
     *
     * @param  Array $params The params for the message.
     */
    protected function replyWithChatAction($params) {
        $params = $this->prepareParams($params);
        $this->telegram->sendChatAction($params);
    }

    /**
     * Prepare the params, and add required fields.
     *
     * @param  Array $params The params to be used to call the Telegram API.
     * @return Array
     */
    private function prepareParams($params) {
        if ($this->update->isType('callback_query')) {
            $params['chat_id'] = $this->update['callback_query']['from']['id'];
            $params['message_id'] = $this->update['callback_query']['message']['message_id'];
        } else {
            $params['chat_id'] = $this->message['chat']['id'];
        }
        return $params;
    }

    /**
     * Defines the rules for when this command should be executed.
     * The default is: The name of the command is sent,
     * or there's a callback query using the defined prefix.
     *
     * @param  Update $update The Telegram update object.
     * @return boolean         Whether the command should be executed.
     */
    public static function shouldExecute($update) {
        if ($update->isType('callback_query')) {
            $data = $update['callback_query']['data'];
            $action = explode('.', $data)[0];

            if ($action == static::PREFIX) {
                return true;
            }
        } elseif (
            isset($update['message']['text']) &&
            explode(' ', $update['message']['text'])[0] == '/' . static::NAME
        ) {
            return true;
        }
        return false;
    }
}
