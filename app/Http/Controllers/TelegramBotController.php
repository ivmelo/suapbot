<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Telegram;

class TelegramBotController extends Controller
{
    public function handleCommands()
    {
        Telegram::addCommand(\App\Telegram\Commands\StartCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\GradesCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\ClassesCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\CalendarioCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\AuthorizeCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\SobreCommand::class);
        Telegram::addCommand(\App\Telegram\Commands\NotifyCommand::class);

        Telegram::commandsHandler(true);

        return response()->json([
            'SUAPBot',
        ], 200);

        // Telegram::addCommand(\App\Telegram\Commands\StartCommand::class);
        //
        // Telegram::commandsHandler(true);
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

                return 'Caught error!' . $e->getMessage();
            }

            return 'Success!';

        } else {
            return view('suapauth.success');
        }

    }
}
