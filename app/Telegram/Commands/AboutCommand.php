<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * This command shows an About page with some basic info about the bot.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class AboutCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'sobre';

    /**
     * {@inheritdoc}
     */
    const ALIASES = [
        'sobre', 'quem', 'ajuda', 'apagar', 'help',
        'remover', 'deletar', 'feedback', 'sair',
    ];

    /**
     * {@inheritdoc}
     */
    const PREFIX = 'about';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Mostra info do bot.';

    /**
     * {@inheritdoc}
     */
    protected function handleCommand($message)
    {
        // Type.
        $this->replyWithChatAction(['action' => 'typing']);

        // And send message.
        $this->replyWithMessage([
            'text'         => Speaker::about(),
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
