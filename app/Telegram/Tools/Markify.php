<?php

namespace App\Telegram\Tools;

/**
 * Markify. Provides helpers to parse boletim data into a readable format.
 * THIS CLASS IS SUPPOSED TO LOOK UGLY. DON'T CHANGE IT IF YOU'RE NOT SURE OF WHAT YOU'RE DOING.
 */
class Markify
{
    public static function parseBoletim($grades) {
        $response_text = '';

        foreach ($grades as $grade) {
            # code...
            $course_info = '*' .$grade['disciplina'] . '*';

            if(isset($grade['aulas']) && $grade['aulas']) {
                $course_info = $course_info . '
' . 'Aulas: ' . $grade['aulas'];
            }

            if(isset($grade['faltas']) && $grade['faltas']) {
                $course_info = $course_info . '
Faltas:  ' . $grade['faltas'] . ' ';
            }

            if (isset($grade['situacao']) && $grade['situacao'] != 'cursando') {
                $course_info = $course_info . '
Situação: ' . ucfirst($grade['situacao']) . ' ';
            }

            if (isset($grade['frequencia']) && $grade['frequencia']) {
                $course_info = $course_info . '
Frequência: ' . $grade['frequencia'] . '% ';
            }

            if (isset($grade['bm1_nota']) && $grade['bm1_nota']) {
                $course_info = $course_info . '
N1: ' . $grade['bm1_nota'] . ' ';
            }

            if (isset($grade['bm2_nota']) && $grade['bm2_nota']) {
                $course_info = $course_info . '
N2: ' . $grade['bm2_nota'] . ' ';
            }

            if (isset($grade['media']) && $grade['media']) {
                $course_info = $course_info . '
Média: ' . $grade['media'] . ' ';
            }

            if (isset($grade['naf_nota']) && $grade['naf_nota']) {
                $course_info = $course_info . '
NAF: ' . $grade['naf_nota'] . ' ';
            }

            if (isset($grade['mfd']) && $grade['mfd']) {
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
