<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;
use Telegram;
use App\Telegram\Tools\Speaker;

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
        return view('home', compact('users'));
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
            'message' => 'required'
        ]);

        $sent = [];
        $not_sent = [];

        foreach ($users as $user) {
            if ($user->telegram_id != null) {
                try{

                    Telegram::sendMessage([
                      'chat_id' => $user->telegram_id,
                      'text' => $request->message,
                      'parse_mode' => 'markdown',
                      'reply_markup' => Speaker::getReplyKeyboardMarkup()
                    ]);

                    array_push($sent, $user);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    array_push($not_sent, [$user, $message]);
                }
            }
            # code...
        }

        return view('report', ['sent' => $sent, 'not_sent' => $not_sent, 'message' => $request->message]);

        // print_r($request->message);
        //
        // print_r($sent);
        //
        // print_r($not_sent);
    }
}
