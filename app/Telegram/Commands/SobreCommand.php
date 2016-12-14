<?php

namespace App\Telegram\Commands;

use App\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Ivmelo\SUAP\SUAP;
use App\Telegram\Tools\Speaker;

class SobreCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'sobre';

    /**
     * @var string Command Description
     */
    protected $description = 'Info sobre o bot.';

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        // Type.
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        // And send message.
        $this->replyWithMessage([
            'text' => Speaker::about(),
            'parse_mode' => 'markdown'
        ]);
    }

}
