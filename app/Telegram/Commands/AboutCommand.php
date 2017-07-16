<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * Help Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class AboutCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'sobre';

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'about';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Mostra info sobre o bot.';

    /**
     * Handles a command call.
     *
     * @param string $message
     */
    protected function handleCommand($message)
    {
        // Type.
        $this->replyWithChatAction(
            ['action' => 'typing']
        );

        // And send message.
        $this->replyWithMessage([
            'text'       => Speaker::about(),
            'parse_mode' => 'markdown',
        ]);
    }

    /**
     * Handles a callback query.
     * This method MUST be implemented, even if it's not used.
     *
     * @param  string $callback_data
     */
    public function handleCallback($callback_data)
    {
        # This method must be implemented...
        return;
    }
}
