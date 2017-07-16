<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * Unknown Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class UnknownCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'unknown';

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'unknown';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Comando não reconhecido.';

    /**
     * Handles a command call.
     *
     * @param string $message
     */
    protected function handleCommand($message)
    {
        // Type.
        $this->replyWithChatAction(['action' => 'typing']);

        // And send message.
        $this->replyWithMessage([
            'text'       => Speaker::unknown(),
            'parse_mode' => 'markdown',
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);
    }

    /**
     * Handles a callback query.
     * This method MUST be implemented, even if it's not used.
     *
     * @param  string $callback_data
     */
    protected function handleCallback($callback_data)
    {
        # This method must be implemented...
        return;
    }
}
