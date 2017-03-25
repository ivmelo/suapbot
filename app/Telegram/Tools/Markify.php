<?php

namespace App\Telegram\Tools;

/**
 * Markify provides helpers to parse boletim data into a readable and organized format.
 */
class Markify
{
    public static function parseBoletim($grades)
    {
        $grades_data = $grades['data'];

        $response_text = '';

        foreach ($grades_data as $grade) {
            $course_info = '*📓 '.$grade['disciplina']."*\n";

            if (isset($grade['aulas'])) {
                $course_info .= 'Aulas: '.$grade['aulas']."\n";
            }

            if (isset($grade['faltas'])) {
                $course_info .= 'Faltas: '.$grade['faltas']."\n";
            }

            if (isset($grade['frequencia'])) {
                $course_info .= 'Frequência: '.$grade['frequencia']."%\n";
            }

            if (isset($grade['bm1_nota'])) {
                $course_info .= 'N1: '.$grade['bm1_nota']."\n";
            }

            if (isset($grade['bm2_nota'])) {
                $course_info .= 'N2: '.$grade['bm2_nota']."\n";
            }

            if (isset($grade['bm3_nota'])) {
                $course_info .= 'N3: '.$grade['bm3_nota']."\n";
            }

            if (isset($grade['bm4_nota'])) {
                $course_info .= 'N4: '.$grade['bm4_nota']."\n";
            }

            if (isset($grade['media'])) {
                $course_info .= 'Média: '.$grade['media']."\n";
            }

            if (isset($grade['naf_nota'])) {
                $course_info .= 'NAF: '.$grade['naf_nota']."\n";
            }

            if (isset($grade['mfd'])) {
                $course_info .= 'MFD/Conceito: '.$grade['mfd']."\n";
            }

            if (isset($grade['situacao']) && $grade['situacao'] != 'cursando') {
                if ($grade['situacao'] == 'aprovado') {
                    $course_info .= '✅ '.ucfirst($grade['situacao'])."\n";
                } else {
                    $course_info .= 'Situação: '.ucfirst($grade['situacao'])."\n";
                }
            }

            $response_text .= $course_info."\n";
        }

        // Total course stats.
        $course_stats = '';

        // if (isset($grades['total_carga_horaria'])) {
        //     $course_stats .= "Total: " . $grades['total_carga_horaria'] . " aulas.\n";
        // }

        if (isset($grades['total_aulas'])) {
            $course_stats .= $grades['total_aulas'].' aulas, ';
        }

        if (isset($grades['total_faltas'])) {
            $course_stats .= $grades['total_faltas']." faltas.\n";
        }

        if (isset($grades['total_frequencia'])) {
            $course_stats .= $grades['total_frequencia']."% de frequência.\n";
        }

        if (isset($grades['total_carga_horaria'])) {
            $course_stats .= 'CH Total: '.$grades['total_carga_horaria']." aulas.\n";
        }

        $response_text .= '*'.$course_stats."*\n";

        return $response_text;
    }
}
