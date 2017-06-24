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

Route::post('webhooks/telegram', 'TelegramBotController@handleWebhook');

Route::auth();

Route::get('home', 'HomeController@index');
Route::post('home', 'HomeController@sendMessage');
Route::get('users', 'UserController@index');

Route::get('auth/{telegram_id}', 'TelegramBotController@getAuth');
Route::post('auth/{telegram_id}', 'TelegramBotController@postAuth');

Route::get('/', function () {
    return redirect('home');
});
