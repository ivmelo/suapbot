<?php

namespace App\Telegram\Tools;

/**
 * Markify provides helpers to parse boletim data into a readable and organized format.
 */
class Markify
{
    public static function parseBoletim($grades)
    {
        $response_text = '';

        // Stats.
        $totalCargaHoraria = 0;
        $totalAulas = 0;
        $totalFaltas = 0;

        foreach ($grades as $grade) {
            $course_info = '*📓 '.explode( ' - ', $grade['disciplina'])[1]."*\n";

            if (isset($grade['carga_horaria'])) {
                $course_info .= 'Aulas: '.$grade['carga_horaria']."\n";
            }

            if (isset($grade['numero_faltas'])) {
                $course_info .= 'Faltas: '.$grade['numero_faltas']."\n";
            }

            if (isset($grade['percentual_carga_horaria_frequentada'])) {
                $course_info .= 'Frequência: '.$grade['percentual_carga_horaria_frequentada']."%\n";
            }

            if (isset($grade['nota_etapa_1']['nota'])) {
                $course_info .= 'N1: '.$grade['nota_etapa_1']['nota']."\n";
            }

            if (isset($grade['nota_etapa_2']['nota'])) {
                $course_info .= 'N2: '.$grade['nota_etapa_2']['nota']."\n";
            }

            if (isset($grade['nota_etapa_3']['nota'])) {
                $course_info .= 'N3: '.$grade['nota_etapa_3']['nota']."\n";
            }

            if (isset($grade['nota_etapa_4']['nota'])) {
                $course_info .= 'N4: '.$grade['nota_etapa_4']['nota']."\n";
            }

            if (isset($grade['media_disciplina'])) {
                $course_info .= 'Média: '.$grade['media_disciplina']."\n";
            }

            if (isset($grade['nota_avaliacao_final']['nota'])) {
                $course_info .= 'NAF: '.$grade['nota_avaliacao_final']['nota']."\n";
            }

            if (isset($grade['media_final_disciplina'])) {
                $course_info .= 'MFD/Conceito: '.$grade['media_final_disciplina']."\n";
            }


            if (isset($grade['situacao']) && $grade['situacao'] != 'Cursando') {
                if ($grade['situacao'] == 'Aprovado') {
                    $course_info .= '✅ '.ucfirst($grade['situacao'])."\n";
                } else {
                    $course_info .= 'Situação: '.ucfirst($grade['situacao'])."\n";
                }
            }

            // Add to stats.
            if (isset($grade['carga_horaria'])) {
                # code...
                $totalCargaHoraria += $grade['carga_horaria'];
                $totalAulas += $grade['carga_horaria_cumprida'];
                $totalFaltas += $grade['numero_faltas'];
            }


            $response_text .= $course_info."\n";
        }

        if ($totalCargaHoraria != 0) {
            // Total course stats.
            $course_stats = '';

            // Calculate total attendance.
            if ($totalFaltas == 0) {
                $attendance = 100;
            } else {
                $attendance = 100 * ($totalAulas - $totalFaltas) / $totalAulas;
            }

            // Write stats.
            $course_stats .= $totalAulas.' aulas, ';
            $course_stats .= $totalFaltas." faltas.\n";
            $course_stats .= round($attendance, 1)."% de frequência.\n";
            $course_stats .= 'CH Total: '.$totalCargaHoraria." aulas.\n";

            // Append to response.
            $response_text .= '*'.$course_stats."*\n";
        }

        return $response_text;
    }
}
