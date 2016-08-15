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

Route::get('job', function () {
    dispatch(new App\Jobs\MonitorReportCardChanges(App\User::find(1)));

    return 'Done!';
});

Route::get('dev/diff', function(){
    // 1 materia a menos
    //$current_json = '[{"diario":7441,"codigo":"TEC.0025","disciplina":"Arquitetura de Software","carga_horaria":80,"aulas":54,"faltas":0,"frequencia":67,"situacao":"cursando","bm1_nota":null,"bm1_faltas":6,"bm2_nota":null,"bm2_faltas":12,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":9693,"codigo":"TEC.0077","disciplina":"Desenvolvimento de Jogos","carga_horaria":80,"aulas":76,"faltas":32,"frequencia":58,"situacao":"cursando","bm1_nota":90,"bm1_faltas":14,"bm2_nota":null,"bm2_faltas":18,"media":36,"naf_nota":null,"naf_faltas":null,"mfd":36},{"diario":7440,"codigo":"TEC.0023","disciplina":"Desenvolvimento de Sistemas Distribu\u00eddos","carga_horaria":120,"aulas":98,"faltas":12,"frequencia":88,"situacao":"cursando","bm1_nota":82,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":10,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7428,"codigo":"TEC.0004","disciplina":"Epistemologia da Ci\u00eancia","carga_horaria":40,"aulas":16,"faltas":16,"frequencia":null,"situacao":"cancelado","bm1_nota":null,"bm1_faltas":16,"bm2_nota":null,"bm2_faltas":null,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7439,"codigo":"TEC.0027","disciplina":"Estrutura de Dados N\u00e3o-Lineares","carga_horaria":80,"aulas":56,"faltas":9,"frequencia":84,"situacao":"cursando","bm1_nota":54,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":7,"media":22,"naf_nota":null,"naf_faltas":null,"mfd":22},{"diario":7436,"codigo":"TEC.0005","disciplina":"Metodologia do Trabalho Cient\u00edfico","carga_horaria":40,"aulas":42,"faltas":10,"frequencia":77,"situacao":"cursando","bm1_nota":70,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":6,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7438,"codigo":"TEC.0024","disciplina":"Processo de Software","carga_horaria":80,"aulas":70,"faltas":14,"frequencia":80,"situacao":"cursando","bm1_nota":88,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":10,"media":35,"naf_nota":null,"naf_faltas":null,"mfd":35},{"diario":7442,"codigo":"TEC.0026","disciplina":"Programa\u00e7\u00e3o e Administra\u00e7\u00e3o de Banco de Dados","carga_horaria":80,"aulas":72,"faltas":13,"frequencia":82,"situacao":"cursando","bm1_nota":null,"bm1_faltas":9,"bm2_nota":null,"bm2_faltas":4,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null}]';

    // dados antigos (FROM DB)
    $current_json = '[{"diario":7441,"codigo":"TEC.0025","disciplina":"Arquitetura de Software","carga_horaria":80,"aulas":54,"faltas":0,"frequencia":67,"situacao":"cursando","bm1_nota":null,"bm1_faltas":6,"bm2_nota":null,"bm2_faltas":12,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":9693,"codigo":"TEC.0077","disciplina":"Desenvolvimento de Jogos","carga_horaria":80,"aulas":78,"faltas":34,"frequencia":57,"situacao":"cursando","bm1_nota":90,"bm1_faltas":14,"bm2_nota":null,"bm2_faltas":18,"media":36,"naf_nota":null,"naf_faltas":null,"mfd":36},{"diario":7440,"codigo":"TEC.0023","disciplina":"Desenvolvimento de Sistemas Distribu\u00eddos","carga_horaria":120,"aulas":98,"faltas":12,"frequencia":88,"situacao":"cursando","bm1_nota":null,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":8,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7428,"codigo":"TEC.0004","disciplina":"Epistemologia da Ci\u00eancia","carga_horaria":40,"aulas":16,"faltas":16,"frequencia":null,"situacao":"cancelado","bm1_nota":null,"bm1_faltas":16,"bm2_nota":null,"bm2_faltas":null,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7439,"codigo":"TEC.0027","disciplina":"Estrutura de Dados N\u00e3o-Lineares","carga_horaria":80,"aulas":56,"faltas":9,"frequencia":84,"situacao":"cursando","bm1_nota":54,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":7,"media":22,"naf_nota":null,"naf_faltas":null,"mfd":22},{"diario":7436,"codigo":"TEC.0005","disciplina":"Metodologia do Trabalho Cient\u00edfico","carga_horaria":40,"aulas":42,"faltas":10,"frequencia":77,"situacao":"cursando","bm1_nota":70,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":6,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7438,"codigo":"TEC.0024","disciplina":"Processo de Software","carga_horaria":80,"aulas":70,"faltas":14,"frequencia":80,"situacao":"cursando","bm1_nota":88,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":10,"media":35,"naf_nota":null,"naf_faltas":null,"mfd":35},{"diario":7442,"codigo":"TEC.0026","disciplina":"Programa\u00e7\u00e3o e Administra\u00e7\u00e3o de Banco de Dados","carga_horaria":80,"aulas":72,"faltas":13,"frequencia":82,"situacao":"cursando","bm1_nota":null,"bm1_faltas":9,"bm2_nota":null,"bm2_faltas":4,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7437,"codigo":"TEC.0033","disciplina":"Semin\u00e1rio de Orienta\u00e7\u00e3o ao Projeto de Desenvolvimento de Sistema Distribu\u00eddo","carga_horaria":40,"aulas":22,"faltas":2,"frequencia":91,"situacao":"cursando","bm1_nota":62,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":null,"media":25,"naf_nota":null,"naf_faltas":null,"mfd":25}]';

    // dados atualizados (FROM SUAP)
    $new_json = '[{"diario":7441,"codigo":"TEC.0025","disciplina":"Arquitetura de Software","carga_horaria":80,"aulas":54,"faltas":18,"frequencia":67,"situacao":"aprovado","bm1_nota":null,"bm1_faltas":6,"bm2_nota":null,"bm2_faltas":12,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":9693,"codigo":"TEC.0077","disciplina":"Desenvolvimento de Jogos","carga_horaria":80,"aulas":76,"faltas":32,"frequencia":58,"situacao":"cursando","bm1_nota":90,"bm1_faltas":14,"bm2_nota":null,"bm2_faltas":18,"media":36,"naf_nota":null,"naf_faltas":null,"mfd":36},{"diario":7440,"codigo":"TEC.0023","disciplina":"Desenvolvimento de Sistemas Distribu\u00eddos","carga_horaria":120,"aulas":98,"faltas":12,"frequencia":88,"situacao":"cursando","bm1_nota":82,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":10,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7428,"codigo":"TEC.0004","disciplina":"Epistemologia da Ci\u00eancia","carga_horaria":40,"aulas":16,"faltas":16,"frequencia":null,"situacao":"cancelado","bm1_nota":null,"bm1_faltas":16,"bm2_nota":null,"bm2_faltas":null,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7439,"codigo":"TEC.0027","disciplina":"Estrutura de Dados N\u00e3o-Lineares","carga_horaria":80,"aulas":56,"faltas":9,"frequencia":84,"situacao":"cursando","bm1_nota":54,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":7,"media":22,"naf_nota":null,"naf_faltas":null,"mfd":22},{"diario":7436,"codigo":"TEC.0005","disciplina":"Metodologia do Trabalho Cient\u00edfico","carga_horaria":40,"aulas":42,"faltas":10,"frequencia":77,"situacao":"cursando","bm1_nota":70,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":6,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7438,"codigo":"TEC.0024","disciplina":"Processo de Software","carga_horaria":80,"aulas":70,"faltas":14,"frequencia":80,"situacao":"cursando","bm1_nota":88,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":10,"media":35,"naf_nota":null,"naf_faltas":null,"mfd":35},{"diario":7442,"codigo":"TEC.0026","disciplina":"Programa\u00e7\u00e3o e Administra\u00e7\u00e3o de Banco de Dados","carga_horaria":80,"aulas":72,"faltas":13,"frequencia":82,"situacao":"cursando","bm1_nota":null,"bm1_faltas":9,"bm2_nota":null,"bm2_faltas":4,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7437,"codigo":"TEC.0033","disciplina":"Semin\u00e1rio de Orienta\u00e7\u00e3o ao Projeto de Desenvolvimento de Sistema Distribu\u00eddo","carga_horaria":40,"aulas":22,"faltas":2,"frequencia":91,"situacao":"cursando","bm1_nota":62,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":null,"media":25,"naf_nota":null,"naf_faltas":null,"mfd":25}]';

    $current_data = json_decode($current_json, true);
    $new_data = json_decode($new_json, true);

    if (count($new_data) != count($current_data)) {
        // One or more courses were added or removed.
        // Notify the user and show their new report card.
        dd('Uma ou mais disciplinas foram adicionadas ou removidas do seu boletim. Digite /notas para ver o seu boletim atualizado!');
    } else {
        $updates = [];

        // Compare course data.
        for ($i = 0; $i < count($current_data); $i++) {
            // Grab data for current course.
            $current_course_data = $current_data[$i];
            $new_course_data = $new_data[$i];

            // Compare the old course data with the new course data.
            if ($updated_data = array_diff_assoc($new_course_data, $current_course_data)) {
                // Add the course name to the list of updated info, so it can be displayed.
                $updated_data['disciplina'] = $current_course_data['disciplina'];
                array_push($updates, $updated_data);
            }
        }

        // If there was an update
        if (count($updates) > 0) {
            // Handle report card update.
            dd($updates);
        } else {
            // Nothing has changed. Do nothing.
            dd('Sem mudanças.');
        }
    }
});

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
    Telegram::addCommand(\App\Telegram\Commands\SobreCommand::class);

    Telegram::commandsHandler(true);
});

Route::get('/', function () {
    return redirect()->to('https://telegram.me/suapbot');
});


/*
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


//Route::auth();

//Route::get('/home', 'HomeController@index');
