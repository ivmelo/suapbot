<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * This command shows the "help" message, explaining how to use the bot.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class CalendarCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    const NAME = 'calendario';

    /**
     * {@inheritDoc}
     */
    const ALIASES = [
        'calendário', 'calend',
        'datas', 'férias', 'ferias'
    ];

    /**
     * {@inheritDoc}
     */
    const PREFIX = 'calendar';

    /**
     * {@inheritDoc}
     */
    const DESCRIPTION = 'Envia o calendário acadêmico atual.';

    /**
     * {@inheritDoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction(['action' => 'typing']);

        $msg = "🗓 Calendário Acadêmico 2017 (Campus Natal Central): \n\n".
        "http://portal.ifrn.edu.br/campus/natalcentral/arquivos/calendario-academico-de-referencia-2017";

        $this->replyWithMessage([
            'text'       => $msg,
            'parse_mode' => 'markdown',
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function handleCallback($callback_data)
    {
        // This method must be implemented...
        return;
    }
}
