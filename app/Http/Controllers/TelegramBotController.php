<?php

namespace App\Http\Controllers;

use Telegram;

class TelegramBotController extends Controller
{
    //
    public function handle()
    {
        Telegram::addCommand(\App\Telegram\Commands\StartCommand::class);

        Telegram::commandsHandler(true);
    }
}
