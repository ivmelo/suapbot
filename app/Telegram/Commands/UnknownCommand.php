<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * Unknown Command. The name says it all.
 * This command is executed whenever the user sends an unknown message.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class UnknownCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'unknown';

    /**
     * {@inheritdoc}
     */
    const PREFIX = 'unknown';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Comando não reconhecido.';

    /**
     * {@inheritdoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction(['action' => 'typing']);

        // Shows the user a brief "how to".
        $this->replyWithMessage([
            'text'         => Speaker::unknown(),
            'parse_mode'   => 'markdown',
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function handleCallback($callback_data)
    {
        // This method must be implemented...
    }
}
