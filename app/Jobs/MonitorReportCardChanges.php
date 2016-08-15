<?php

namespace App\Jobs;

use Telegram;
use App\User;
use App\Jobs\Job;
use App\Telegram\Tools\Markify;
use \Ivmelo\SUAPClient\SUAPClient;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/*
 * This Job monitors if the user's report card (boletim) has changes
 * and notifies the user in case there is.
 */
class MonitorReportCardChanges extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        // Get user.
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Current data from database.
        $current_json = '[{"diario":7441,"codigo":"TEC.0025","disciplina":"Arquitetura de Software","carga_horaria":80,"aulas":54,"faltas":18,"frequencia":67,"situacao":"cursando","bm1_nota":null,"bm1_faltas":6,"bm2_nota":null,"bm2_faltas":12,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":9693,"codigo":"TEC.0077","disciplina":"Desenvolvimento de Jogos","carga_horaria":80,"aulas":76,"faltas":32,"frequencia":58,"situacao":"cursando","bm1_nota":90,"bm1_faltas":14,"bm2_nota":null,"bm2_faltas":18,"media":36,"naf_nota":null,"naf_faltas":null,"mfd":36},{"diario":7440,"codigo":"TEC.0023","disciplina":"Desenvolvimento de Sistemas Distribu\u00eddos","carga_horaria":120,"aulas":98,"faltas":12,"frequencia":88,"situacao":"cursando","bm1_nota":82,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":10,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7428,"codigo":"TEC.0004","disciplina":"Epistemologia da Ci\u00eancia","carga_horaria":40,"aulas":0,"faltas":0,"frequencia":null,"situacao":"cursando","bm1_nota":null,"bm1_faltas":16,"bm2_nota":null,"bm2_faltas":null,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7439,"codigo":"TEC.0027","disciplina":"Estrutura de Dados N\u00e3o-Lineares","carga_horaria":80,"aulas":56,"faltas":9,"frequencia":84,"situacao":"cursando","bm1_nota":54,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":7,"media":22,"naf_nota":null,"naf_faltas":null,"mfd":22},{"diario":7436,"codigo":"TEC.0005","disciplina":"Metodologia do Trabalho Cient\u00edfico","carga_horaria":40,"aulas":42,"faltas":10,"frequencia":77,"situacao":"cursando","bm1_nota":70,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":6,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7438,"codigo":"TEC.0024","disciplina":"Processo de Software","carga_horaria":80,"aulas":70,"faltas":14,"frequencia":80,"situacao":"cursando","bm1_nota":88,"bm1_faltas":4,"bm2_nota":null,"bm2_faltas":10,"media":35,"naf_nota":null,"naf_faltas":null,"mfd":35},{"diario":7442,"codigo":"TEC.0026","disciplina":"Programa\u00e7\u00e3o e Administra\u00e7\u00e3o de Banco de Dados","carga_horaria":80,"aulas":72,"faltas":13,"frequencia":82,"situacao":"cursando","bm1_nota":null,"bm1_faltas":9,"bm2_nota":null,"bm2_faltas":4,"media":null,"naf_nota":null,"naf_faltas":null,"mfd":null},{"diario":7437,"codigo":"TEC.0033","disciplina":"Semin\u00e1rio de Orienta\u00e7\u00e3o ao Projeto de Desenvolvimento de Sistema Distribu\u00eddo","carga_horaria":40,"aulas":22,"faltas":2,"frequencia":91,"situacao":"cursando","bm1_nota":62,"bm1_faltas":2,"bm2_nota":null,"bm2_faltas":null,"media":25,"naf_nota":null,"naf_faltas":null,"mfd":25}]';//$this->user->course_data;

        try {
            // Get grades from SUAP.
            $client = new SUAPClient($this->user->suap_id, $this->user->suap_key, true);
            $new_data = $client->getGrades();

            $current_data = json_decode($current_json, true);

            if (count($new_data) != count($current_data)) {
                // One or more courses were added or removed.
                // Notify the user and show their new report card.
                Telegram::sendChatAction([
                    'chat_id' => $this->user->telegram_id,
                    'action' => Telegram\Bot\Actions::TYPING
                ]);

                $message = [
                    'chat_id' => $this->user->telegram_id,
                    'text' => 'Uma ou mais disciplinas foram adicionadas ou removidas do seu boletim. Digite /notas para ver o seu boletim atualizado.',
                    'parse_mode' => 'markdown'
                ];

                Telegram::sendMessage($message);

                // debug only
                print('Report card change. User notified.');
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

                    Telegram::sendChatAction([
                        'chat_id' => $this->user->telegram_id,
                        'action' => Telegram\Bot\Actions::TYPING
                    ]);

                    // Parse grades into a readable format.
                    $grades_response = Markify::parseBoletim($updates);

                    $grades_response = "_Boletim Atualizado_\n"
                        . $grades_response . "Digite /notas para ver o boletim completo.";

                    // Send grades to the user.
                    $message = [
                        'chat_id' => $this->user->telegram_id,
                        'text' => $grades_response,
                        'parse_mode' => 'markdown'
                    ];

                    Telegram::sendMessage($message);

                    // Save report card updates.
                    $course_data_json = json_encode($new_data);
                    $this->user->course_data = $course_data_json;
                    $this->user->save();

                } else {
                    // Nothing has changed. Do nothing.
                    print('No changes.');
                }
            }

        } catch (\Exception $e) {
            // Error fetching data from SUAP, or parsing report card data.
            print('Exception: ' . $e->getMessage());
        }

    }
}
