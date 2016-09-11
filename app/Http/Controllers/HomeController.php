<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\User;

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
        // $users = User::all();

        $this->validate($request, [
            'message' => 'required'
        ]);

        $sent = [];
        $not_sent = [];

        foreach ($users as $user) {
            if ($user->telegram_id != null) {
                # code...
            }
            # code...
        }

        dd($request->message);
    }
}
