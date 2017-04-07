<?php

namespace App\Http\Controllers;

use App\Telegram\Tools\Speaker;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        $stats['total'] = $users->count();
        $stats['active'] = User::where('suap_id', '!=', 'null')->count();
        $stats['today'] = User::where('created_at', 'like', Carbon::now()->toDateString().'%')->count();
        $stats['week'] = User::whereBetween('created_at', [Carbon::today()->subWeek()->toDateString().'%', Carbon::today()->toDateString().'%'])->count();

        return view('home', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }

    /**
     * Send a message to all users.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request)
    {
        $users = User::all();

        $this->validate($request, [
            'message' => 'required',
        ]);

        $sent = [];
        $not_sent = [];

        foreach ($users as $user) {
            if ($user->telegram_id != null) {
                try {
                    Telegram::sendMessage([
                      'chat_id'      => $user->telegram_id,
                      'text'         => $request->message,
                      'parse_mode'   => 'markdown',
                      'reply_markup' => Speaker::getReplyKeyboardMarkup(),
                    ]);

                    array_push($sent, $user);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    array_push($not_sent, ['user' => $user, 'message' => $message]);
                }
            }
        }

        return view('report', [
            'sent'     => $sent,
            'not_sent' => $not_sent,
            'message'  => $request->message,
        ]);
    }
}
