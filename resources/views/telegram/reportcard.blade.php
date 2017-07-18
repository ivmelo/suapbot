@if($update) 📚 *BOLETIM ATUALIZADO:* @else 📚 *BOLETIM:* @endif


@foreach ($grades as $grade)
*📓 {!! explode( ' - ', $grade['disciplina'])[1] !!}*
@if (isset($grade['carga_horaria_cumprida']))
Aulas: {{ $grade['carga_horaria_cumprida'] }}
@endif
@if (isset($grade['numero_faltas']))
Faltas: {{ $grade['numero_faltas'] }}
@endif
@if (isset($grade['percentual_carga_horaria_frequentada']))
Frequência: {{ $grade['percentual_carga_horaria_frequentada'] }}%
@endif
@if (isset($grade['nota_etapa_1']['nota']))
N1: {{ $grade['nota_etapa_1']['nota'] }}
@endif
@if (isset($grade['nota_etapa_2']['nota']))
N2: {{ $grade['nota_etapa_2']['nota'] }}
@endif
@if (isset($grade['nota_etapa_3']['nota']))
N3: {{ $grade['nota_etapa_3']['nota'] }}
@endif
@if (isset($grade['nota_etapa_4']['nota']))
N4: {{ $grade['nota_etapa_4']['nota'] }}
@endif
@if (isset($grade['media_disciplina']))
Média: {{ $grade['media_disciplina'] }}
@endif
@if (isset($grade['nota_avaliacao_final']['nota']))
NAF: {{ $grade['nota_avaliacao_final']['nota'] }}
@endif
@if (isset($grade['media_final_disciplina']))
MFD/Conceito: {{ $grade['media_final_disciplina'] }}
@endif
@if (isset($grade['situacao']) && $grade['situacao'] != 'Cursando')
@if ($grade['situacao'] == 'Aprovado')
✅ {{ ucfirst($grade['situacao'])}}
@else
Situação: {{ ucfirst($grade['situacao']) }}
@endif
@endif

@endforeach
*{{ $stats['total_aulas'] }} aulas, {{ $stats['total_faltas'] }} faltas.
{{ round($stats['frequencia'], 1) }}% de frequência.
CH Total: {{ $stats['total_carga_horaria'] }} aulas.*

@if($update) Digite /boletim para ver o seu boletim completo ou /ajustes para configurar as notificações. @endif
