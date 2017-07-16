<?php

namespace App\Http\Controllers;

use Bugsnag;
use App\User;
use Telegram\Bot\Api;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Foundation\Inspiring;

use App\Telegram\Commands\NewSettingsCommand;
use App\Telegram\Commands\ClassMaterialCommand;

use App\Telegram\Commands\CalendarCommand;
use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\StartCommand;

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
        // \App\Telegram\Commands\StartCommand::class,
        \App\Telegram\Commands\GradesCommand::class,
        \App\Telegram\Commands\ClassesCommand::class,
        // \App\Telegram\Commands\CalendarioCommand::class,
        \App\Telegram\Commands\AuthorizeCommand::class,
        // \App\Telegram\Commands\SobreCommand::class,
        \App\Telegram\Commands\GradesAliasCommand::class,
    ];

    /**
     * The public constructor of this class.
     *
     * @param \Telegram\Bot\Api $telegram
     */
    public function handleWebhook()
    {
        $this->telegram->addCommands($this->commands);
        $update = $this->telegram->commandsHandler(true);

        // $update = $this->telegram->getUpdates();

        $message = $update->getMessage();

        if ($update->isType('callback_query')) {
            if (strrpos($update['callback_query']['data'], 'settings') === 0) {
                $ns = new NewSettingsCommand($this->telegram, $update);
                $ns->handle();
            } elseif (strrpos($update['callback_query']['data'], 'turmas') === 0) {
                $ns = new ClassMaterialCommand($this->telegram, $update);
                $ns->handle();
            }
        } elseif (isset($message['text'])) {
            $message_text = strtolower($message['text']);

            if (str_contains($message_text, [NewSettingsCommand::NAME])) {
                $ns = new NewSettingsCommand($this->telegram, $update);
                $ns->handle();
            } elseif (str_contains($message_text, [ClassMaterialCommand::NAME])) {
                $ns = new ClassMaterialCommand($this->telegram, $update);
                $ns->handle();
            } elseif (str_contains($message_text, ['inspire', 'inspirational', 'inspiring', 'inspirar'])) {
                $this->telegram->sendMessage([
                    'chat_id' => $message['chat']['id'],
                    'text' => Inspiring::quote(),
                ]);
            } elseif (str_contains($message_text, ['obrigado', 'valeu', 'thanks', 'thx'])) {
                $this->telegram->sendMessage([
                    'chat_id' => $message['chat']['id'],
                    'text' => ':)',
                ]);
            } elseif (str_contains($message_text, ['notas', 'boletim', 'faltas'])) {
                $this->telegram->triggerCommand('boletim', $update);
            } elseif (str_contains($message_text, ['aulas', 'horário', 'sala', 'aula'])) {
                $this->telegram->triggerCommand('aulas', $update);
            } elseif (str_contains($message_text, ['calendário', 'calendario'])) {
                // $this->telegram->triggerCommand('calendario', $update);
                $command = new CalendarCommand($this->telegram, $update);
                $command->handle();
            } elseif (str_contains($message_text, ['start'])) {
                $command = new StartCommand($this->telegram, $update);
                $command->handle();
            } elseif (str_contains($message_text, [
                    'sobre', 'quem', 'ajuda', 'apagar', 'help',
                    'remover', 'deletar', 'feedback', 'sair'
                ])) {
                $command = new AboutCommand($this->telegram, $update);
                $command->handle();
                // $this->telegram->triggerCommand('sobre', $update);
            }
        }

        return response()->json([
            'SUAPBot',
        ], 200);
    }

    /**
     * Handles the get request from the auth form.
     *
     * @param integer $telegram_id
     * @param Response
     */
    public function getAuth($telegram_id) {
        $user = User::where('telegram_id', '=', $telegram_id)->firstOrFail();

        if (! $user->suap_id && ! $user->suap_key) {
            return view('suapauth.auth', compact('user'));
        } else {
            return view('suapauth.success');
        }

        abort(404);
    }

    /**
     * Handles the post request from the auth form.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $telegram_id
     * @param Response
     */
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
