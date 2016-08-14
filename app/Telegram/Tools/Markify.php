<?php

namespace App\Telegram\Tools;

/**
 * Markfy. Provides helpers to parse boletim data.
 */
class Markify
{
    public static function parseBoletim($grades) {
        $response_text = '';

        foreach ($grades as $grade) {
            # code...
            $course_info = '*' .$grade['disciplina'] . '*
' . 'Aulas: ' . $grade['aulas'] . '
Faltas:  ' . $grade['faltas'] . ' ';

            if ($grade['situacao'] != 'cursando') {
                $course_info = $course_info . '
Situação: ' . ucfirst($grade['situacao']) . ' ';
            }

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

        return $response_text;
    }
}
