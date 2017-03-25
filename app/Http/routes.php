<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('day_name', function () {
    return App\Telegram\Tools\Speaker::getDayOfTheWeek(1, true);
});

Route::post('webhooks/telegram', function () {
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
});

Route::auth();

Route::get('home', 'HomeController@index');
Route::post('home', 'HomeController@sendMessage');
Route::get('users', 'UserController@index');

Route::get('/', function () {
    return redirect('home');
});
