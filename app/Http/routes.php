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

Route::post('webhooks/' . config('app.telegram_webhook_secret') . '/telegram', 'SUAPBotController@handleWebhook');

Route::post('webhooks/telegram/setup', function(){
    $url = secure_url('webhooks/' . config('app.telegram_webhook_secret') . '/telegram');
    try {
        $response = Telegram::setWebhook(['url' => $url]);
        return response()->json('Ok!', 200);
    } catch (Exception $e) {
        return response()->json('Error!', 400);
    }
});

Route::auth();

Route::get('home', 'HomeController@index');
Route::post('home', 'HomeController@sendMessage');

Route::get('auth/{telegram_id}', 'SUAPBotController@getAuth');
Route::post('auth/{telegram_id}', 'SUAPBotController@postAuth');

Route::get('/', function () {
    return redirect('home');
});
