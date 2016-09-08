<?php

namespace App\Telegram\Tools;

/**
 * Markify provides helpers to parse boletim data into a readable and organized format.
 */
class Markify
{
    public static function parseBoletim($grades) {
        $response_text = '';

        foreach ($grades as $grade) {
            $course_info = "*" . $grade['disciplina'] . "*\n";

            if(isset($grade['aulas']) && $grade['aulas']) {
                $course_info .= "Aulas: " . $grade['aulas'] . "\n";
            }

            if(isset($grade['faltas']) && $grade['faltas']) {
                $course_info .= "Faltas: " . $grade['faltas'] . "\n";
            }

            if (isset($grade['situacao']) && $grade['situacao'] != 'cursando') {
                $course_info .= "Situação: " . ucfirst($grade['situacao']) . "\n";
            }

            if (isset($grade['frequencia']) && $grade['frequencia']) {
                $course_info .= "Frequência: " . $grade['frequencia'] . "%\n";
            }

            if (isset($grade['bm1_nota']) && $grade['bm1_nota']) {
                $course_info .= "N1: " . $grade['bm1_nota'] . "\n";
            }

            if (isset($grade['bm2_nota']) && $grade['bm2_nota']) {
                $course_info .= "N2: " . $grade['bm2_nota'] . "\n";
            }

            if (isset($grade['bm3_nota']) && $grade['bm3_nota']) {
                $course_info .= "N3: " . $grade['bm3_nota'] . "\n";
            }

            if (isset($grade['bm4_nota']) && $grade['bm4_nota']) {
                $course_info .= "N4: " . $grade['bm4_nota'] . "\n";
            }

            if (isset($grade['media']) && $grade['media']) {
                $course_info .= "Média: " . $grade['media'] . "\n";
            }

            if (isset($grade['naf_nota']) && $grade['naf_nota']) {
                $course_info .= "NAF: " . $grade['naf_nota'] . "\n";
            }

            if (isset($grade['mfd']) && $grade['mfd']) {
                $course_info .= "MFD/Conceito: " . $grade['mfd'] . "\n";
            }

            $response_text .= $course_info . "\n";
        }

        return $response_text;
    }
}
