<?php

namespace App\Telegram\Commands;

use Bugsnag;
use App\Telegram\Tools\Markify;
use App\Telegram\Tools\Speaker;
use App\User;
use Ivmelo\SUAP\SUAP;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use View;

class GradesAliasCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'notas';

    /**
     * @var string Command Description
     */
    protected $description = 'Alias do comando /boletim.';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        // User was not found.
        $this->replyWithMessage([
            'text' => 'Por favor, para ver suas notas use o comando /boletim.',
            'reply_markup' => Speaker::getReplyKeyboardMarkup(),
        ]);

    }
}
