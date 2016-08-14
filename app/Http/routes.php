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

Route::post('webhooks/telegrama', function(){
    // error message
    $updates = Telegram::getWebhookUpdates();

    $chat_id = $updates['message']['from']['id'];
    $first_name = $updates['message']['from']['first_name'];

    Telegram::sendChatAction([
        'chat_id' => $chat_id,
        'action' => Telegram\Bot\Actions::TYPING
    ]);

    $message = [
        'chat_id' => $chat_id,
        'text' => 'Olá, ' . $first_name . '. Infelizmente, alguém enviou um comando que me quebrou. Mas não se preocupe, já estou sendo consertado e logo logo voltarei a funcionar. :D'
    ];

    $response = Telegram::sendMessage($message);
});

Route::post('webhooks/telegram', function(){
    Telegram::addCommand(\App\Telegram\Commands\StartCommand::class);
    Telegram::addCommand(\App\Telegram\Commands\GradesCommand::class);
    Telegram::addCommand(\App\Telegram\Commands\AuthorizeCommand::class);

    Telegram::commandsHandler(true);



/*
    $updates = Telegram::getWebhookUpdates();
    $telegram_id = $updates['message']['from']['id'];
    $text = $updates['message']['text'];
    $user = User::where('telegram_id', $telegram_id)->first();

    if (! $user->suap_id) {
        $user->suap_id = $text;
        $user->save();
    }*/
});



Route::post('webhooks/telegrama', function(){
    //echo Telegram::getMe();

    //Telegram::addCommand(App\Telegram\Commands\StartCommand::class);

    $updates = Telegram::getWebhookUpdates();


    $chat_id = $updates['message']['from']['id'];
    $name = $updates['message']['from']['first_name'];


    Telegram::sendChatAction([
        'chat_id' => $chat_id,
        'action' => Telegram\Bot\Actions::TYPING
    ]);


$json_updates = json_encode($updates);

$message = [
    'chat_id' => $chat_id,
    'text' => 'Olá, ' . $name . '! Eu sou o SUAP Bot, eu posso te mostrar informações sobre suas notas e faltas. Também posso te enviar notificações quando novas notas ou faltas forem lançadas. Para começar, eu preciso saber o seu IFRN ID (Geralmente o seu número de matrícula) para que eu possa ter acesso ao teu boletim.'
];


// SUAP
$text = $updates['message']['text'];
$credentials = explode(' ', $text);

$client = new \Ivmelo\SUAPClient\SUAPClient($credentials[0], $credentials[1], true);

$grades = $client->getGrades();

$response_text = '';

// notas e faltas
foreach ($grades as $grade) {
    # code...
    $course_info = '*' .$grade['disciplina'] . '*
' . 'Aulas: ' . $grade['aulas'] . '
Faltas:  ' . $grade['faltas'] . ' ';

    if ($grade['frequencia']) {
        $course_info = $course_info . '
Frequência: ' . $grade['frequencia'] . '% ';
    }

    if ($grade['bm1_nota']) {
        $course_info = $course_info . '
N1: ' . $grade['bm1_nota'] . ' ';
    }

    if ($grade['bm2_nota']) {
        $course_info = $course_info . '
N2: ' . $grade['bm2_nota'] . ' ';
    }

    if ($grade['media']) {
        $course_info = $course_info . '
Média: ' . $grade['media'] . ' ';
    }

    if ($grade['naf_nota']) {
        $course_info = $course_info . '
NAF: ' . $grade['naf_nota'] . ' ';
    }

    if ($grade['mfd']) {
        $course_info = $course_info . '
NAF: ' . $grade['mfd'];
    }

    $course_info = $course_info . '

';

    $response_text =  $response_text . $course_info;
}

$grades_text = json_encode($grades[0]['disciplina']);

$message2 = [
    'chat_id' => $chat_id,
    'text' => $response_text,//$updates['message']['text'],
    'parse_mode' => 'markdown'
];

$response = Telegram::sendMessage($message2);

    Telegram::addCommand(App\Telegram\Commands\StartCommand::class);


});

/*
Route::get('/suap', function(){
    return view('suaplogin');
});

Route::post('/suap', function(Illuminate\Http\Request $request){

    $suap_client = new Ivmelo\SUAPClient\SUAPClient($request->ifrn_id, $request->access_key, true);

    $request->session()->put('access_key', $request->access_key);

    $data = $suap_client->getStudentData();
    $course_data = $suap_client->getGrades();
    //dd($data);

    return view('register', ['std_data' => $data, 'course_data' => $course_data]);
});

Route::post('/continue', function(Illuminate\Http\Request $request){
    return 'worked';
});
*/

Route::get('/', function () {
    return '<a href="https://telegram.me/suapbot">https://telegram.me/suapbot</a>';

    //return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');
