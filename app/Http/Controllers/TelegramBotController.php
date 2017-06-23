<?php

namespace App\Http\Controllers;

use Bugsnag;
use Illuminate\Http\Request;
use App\User;
use Telegram\Bot\Api;
use Illuminate\Foundation\Inspiring;
use Telegram\Bot\Keyboard\Keyboard;

use App\Telegram\Commands\NewSettingsCommand;

class TelegramBotController extends Controller
{

    /**
     * The telegram API client.
     *
     * @var \Telegram\Bot\Api
     */
    protected $telegram;

    /**
     * The public constructor of this class.
     *
     * @param \Telegram\Bot\Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    protected $commands = [
        \App\Telegram\Commands\SettingsCommand::class,
    ];

    /**
     * The public constructor of this class.
     *
     * @param \Telegram\Bot\Api $telegram
     */
    public function handleWebhook()
    {
        $this->telegram->addCommand(\App\Telegram\Commands\StartCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\GradesCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\ClassesCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\CalendarioCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\AuthorizeCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\SobreCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\NotifyCommand::class);
        $this->telegram->addCommand(\App\Telegram\Commands\GradesAliasCommand::class);
        // $this->telegram->addCommand(\App\Telegram\Commands\SettingsCommand::class);

        $update = $this->telegram->commandsHandler(true);

        $message = $update->getMessage();


        if ($update->isType('callback_query')) {
            $ns = new NewSettingsCommand($this->telegram, $update);
            $ns->handle();
        }

        if (str_contains($message['text'], [NewSettingsCommand::NAME])) {
            // $query = $update->getCallbackQuery();
            $ns = new NewSettingsCommand($this->telegram, $update);
            $ns->handle();

        } elseif (str_contains($message['text'], ['inspire', 'inspirational', 'inspiring', 'inspirar'])) {
            $this->telegram->sendMessage([
                'chat_id' => $message['chat']['id'],
                'text' => Inspiring::quote(),
            ]);
        } elseif (str_contains($message['text'], ['obrigado', 'valeu', 'thanks', 'thx'])) {
            $this->telegram->sendMessage([
                'chat_id' => $message['chat']['id'],
                'text' => ':)',
            ]);
        } elseif (str_contains($message['text'], ['notas', 'boletim', 'faltas'])) {
            $this->telegram->triggerCommand('boletim', $update);
        } elseif (str_contains($message['text'], ['aulas', 'horário', 'sala', 'aula'])) {
            $this->telegram->triggerCommand('aulas', $update);
        }

        // else {
        //     $this->telegram->sendMessage([
        //         'chat_id' => $message['chat']['id'],
        //         'text' => 'Desculpa, não entendi. Me pergunte sobre suas notas, faltas ou aulas. (Ex. quais as minhas aulas de amanhã?, Mostre meu boletim.) ou tente enviar um dos comandos a seguir: /boletim, /aulas, /config.',
        //     ]);
        // }

        return response()->json([
            'SUAPBot',
        ], 200);
    }

    public function getAuth($telegram_id) {
        $user = User::where('telegram_id', '=', $telegram_id)->firstOrFail();

        if (! $user->suap_id && ! $user->suap_key) {
            return view('suapauth.auth', compact('user'));
        } else {
            return view('suapauth.success');
        }

        abort(404);
    }

    public function postAuth(Request $request, $telegram_id) {
        $this->validate($request, [
            'suapid' => 'required|integer',
            'suapkey' => 'required'
        ]);

        $user = User::where('telegram_id', '=', $telegram_id)->firstOrFail();

        if (! $user->suap_id && ! $user->suap_key) {
            try {
                $user->authorize($request->get('suapid'), $request->get('suapkey'));
                return view('suapauth.success');
            } catch (\Exception $e) {
                // return redirect()->back()->with('error_message', 'Erro ao autorizar o seu acesso.');
                Bugsnag::notifyException($e);
                return 'Caught error!' . $e->getMessage();
            }

            return 'Success!';

        } else {
            return view('suapauth.success');
        }

    }
}
