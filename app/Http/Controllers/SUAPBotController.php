<?php

namespace App\Http\Controllers;

use Bugsnag;
use App\User;
use Telegram\Bot\Api;
use Illuminate\Http\Request;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Foundation\Inspiring;

use App\Telegram\Commands\ClassesCommand;
use App\Telegram\Commands\CalendarCommand;
use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\SettingsCommand;
use App\Telegram\Commands\ReportCardCommand;
use App\Telegram\Commands\ClassScheduleCommand;
use App\Telegram\Commands\UnknownCommand;

class SUAPBotController extends Controller
{

    /**
     * The telegram API client.
     *
     * @var \Telegram\Bot\Api
     */
    protected $telegram;

    /**
     * The Telegram update object.
     *
     * @var Telegram\Bot\Objects\Update
     */
    private $update;

    /**
     * The public constructor of this class.
     *
     * @param \Telegram\Bot\Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->update = $telegram->getWebhookUpdates();
    }

    /**
     * The public constructor of this class.
     *
     * @param \Telegram\Bot\Api $telegram
     */
    public function handleWebhook()
    {
        $message = $this->update->getMessage();

        if ($this->update->isType('callback_query')) {
            if (strrpos($this->update['callback_query']['data'], 'settings') === 0) {
                $command = new SettingsCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (strrpos($this->update['callback_query']['data'], 'classes') === 0) {
                $command = new ClassesCommand($this->telegram, $this->update);
                $command->handle();
            }
        } elseif (isset($message['text'])) {
            $message_text = strtolower($message['text']);

            if (str_contains($message_text, [SettingsCommand::NAME])) {
                $command = new SettingsCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (str_contains($message_text, [ClassesCommand::NAME])) {
                $command = new ClassesCommand($this->telegram, $this->update);
                $command->handle();
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
                // $this->telegram->triggerCommand('boletim', $this->update);
                $command = new ReportCardCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (str_contains($message_text, ['aulas', 'horário', 'sala', 'aula'])) {
                // $this->telegram->triggerCommand('aulas', $this->update);
                $command = new ClassScheduleCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (str_contains($message_text, ['calendário', 'calendario'])) {
                // $this->telegram->triggerCommand('calendario', $this->update);
                $command = new CalendarCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (str_contains($message_text, ['start'])) {
                $command = new StartCommand($this->telegram, $this->update);
                $command->handle();
            } elseif (str_contains($message_text, [
                    'sobre', 'quem', 'ajuda', 'apagar', 'help',
                    'remover', 'deletar', 'feedback', 'sair'
                ])) {
                $command = new AboutCommand($this->telegram, $this->update);
                $command->handle();
                // $this->telegram->triggerCommand('sobre', $this->update);
            } else {
                $command = new UnknownCommand($this->telegram, $this->update);
                $command->handle();
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

            $result = $user->authorize($request->get('suapid'), $request->get('suapkey'));

            if ($result) {
                return view('suapauth.success');
            } else {
                return redirect()->back()->with('danger_message', 'Erro ao autenticar! Por favor, verifique a sua matrícula e chave de acesso, e tente novamente.');
            }

            return 'Success!';
        } else {
            return view('suapauth.success');
        }

    }
}
