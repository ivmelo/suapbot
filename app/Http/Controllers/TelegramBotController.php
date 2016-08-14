<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Telegram;

use App\Telegram\Commands\StartCommand;

class TelegramBotController extends Controller
{
    //
    public function handle() {

        Telegram::addCommand(\App\Telegram\Commands\StartCommand::class);

        Telegram::commandsHandler(true);

    }
}
