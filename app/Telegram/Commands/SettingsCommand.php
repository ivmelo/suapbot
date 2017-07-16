<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * Settings Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class SettingsCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'ajustes';

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'settings';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Mostra painel de ajustes.';

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
