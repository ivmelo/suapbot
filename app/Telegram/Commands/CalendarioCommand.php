<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAPClient\SUAPClient;
use App\Telegram\Tools\Speaker;

class CalendarioCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'calendario';

    /**
     * @var string Command Description
     */
    protected $description = 'Envia calendário acadêmico.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // Type.
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // And send message.
        $this->replyWithMessage([
            'text' => "Calendário Acadêmico 2016: \n\nhttp://portal.ifrn.edu.br/campus/natalcentral/arquivos/calendario-escolar-2016-aprovado-pelo-conselho-escolar",
            'parse_mode' => 'markdown'
        ]);
    }

}
