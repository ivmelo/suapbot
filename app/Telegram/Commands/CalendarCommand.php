<?php

namespace App\Telegram\Commands;

use App\Telegram\Tools\Speaker;

/**
 * Help Command.
 *
 * @author Ivanilson Melo <meloivanilson@gmail.com>
 */
class CalendarCommand extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    const NAME = 'calendario';

    /**
     * The aliases of this command.
     * @var array
     */
    const ALIASES = [
        'calendário', 'calend',
        'datas', 'férias', 'ferias'
    ];

    /**
     * The prefix for callback queries.
     *
     * @var string
     */
    const PREFIX = 'calendar';

    /**
     * The description of the command.
     *
     * @var string
     */
    const DESCRIPTION = 'Envia o calendário acadêmico atual.';

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

        $msg = "🗓 Calendário Acadêmico 2017 (Campus Natal Central): \n\nhttp://portal.ifrn.edu.br/campus/natalcentral/arquivos/calendario-academico-de-referencia-2017";

        // And send message.
        $this->replyWithMessage([
            'text'       => $msg,
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
