<?php

namespace App\Http\Controllers;

use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\CalendarCommand;
use App\Telegram\Commands\ClassesCommand;
use App\Telegram\Commands\ClassScheduleCommand;
use App\Telegram\Commands\Command;
use App\Telegram\Commands\ReportCardCommand;
use App\Telegram\Commands\SettingsCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\UnknownCommand;
use App\User;
use Illuminate\Http\Request;
use Telegram\Bot\Api;

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
     * Handles the webhook update.
     * This is where you register and execute a command.
     */
    public function handleWebhook()
    {

        // Register commands...
        if (SettingsCommand::shouldExecute($this->update)) {
            $command = new SettingsCommand($this->telegram, $this->update);
        } elseif (ClassesCommand::shouldExecute($this->update)) {
            $command = new ClassesCommand($this->telegram, $this->update);
        } elseif (ReportCardCommand::shouldExecute($this->update)) {
            $command = new ReportCardCommand($this->telegram, $this->update);
        } elseif (ClassScheduleCommand::shouldExecute($this->update)) {
            $command = new ClassScheduleCommand($this->telegram, $this->update);
        } elseif (CalendarCommand::shouldExecute($this->update)) {
            $command = new CalendarCommand($this->telegram, $this->update);
        } elseif (StartCommand::shouldExecute($this->update)) {
            $command = new StartCommand($this->telegram, $this->update);
        } elseif (AboutCommand::shouldExecute($this->update)) {
            $command = new AboutCommand($this->telegram, $this->update);
        } else {
            // This one will run by default.
            $command = new UnknownCommand($this->telegram, $this->update);
        }

        // Run the command:
        if (isset($command) && $command instanceof Command) {
            $command->handle();
        }

        return response()->json([
            'SUAPBot',
        ], 200);
    }

    /**
     * Handles the get request from the auth form.
     *
     * @param int $telegram_id
     * @param Response
     */
    public function getAuth($telegram_id)
    {
        $user = User::where('telegram_id', '=', $telegram_id)->firstOrFail();

        if (!$user->suap_id && !$user->suap_key) {
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
     * @param int                      $telegram_id
     * @param Response
     */
    public function postAuth(Request $request, $telegram_id)
    {
        $this->validate($request, [
            'suapid'  => 'required|integer',
            'suapkey' => 'required',
        ]);

        $user = User::where('telegram_id', '=', $telegram_id)->firstOrFail();

        if (!$user->suap_id && !$user->suap_key) {
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
