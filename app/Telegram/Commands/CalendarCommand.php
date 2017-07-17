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
     * {@inheritdoc}
     */
    const NAME = 'calendario';

    /**
     * {@inheritdoc}
     */
    const ALIASES = [
        'calendário', 'calend',
        'datas', 'férias', 'ferias',
    ];

    /**
     * {@inheritdoc}
     */
    const PREFIX = 'calendar';

    /**
     * {@inheritdoc}
     */
    const DESCRIPTION = 'Envia o calendário acadêmico atual.';

    /**
     * {@inheritdoc}
     */
    protected function handleCommand($message)
    {
        $this->replyWithChatAction(['action' => 'typing']);

        $msg = "🗓 Calendário Acadêmico 2017 (Campus Natal Central): \n\n".
        'http://portal.ifrn.edu.br/campus/natalcentral/arquivos/calendario-academico-de-referencia-2017';

        $this->replyWithMessage([
            'text'         => $msg,
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
