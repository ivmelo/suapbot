@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Autorização</h2>

    <p>Para usar o SUAP bot, primeiro é preciso fornecer a sua matrícula e chave de acesso de responsável do SUAP.</p>

    <p>A sua chave de acesso <strong>não é a sua senha</strong>. Ela permite apenas a leitura dos seus dados do SUAP, tais como notas, faltas, horários de aula e etc...</p>

    <p>Para pegar a sua chave, basta seguir os seguintes passos:</p>

    <ol>
        <li>Faça login no <a href="https://suap.ifrn.edu.br" target="_blank">SUAP</a>.</li>
        <li>Navegue até <strong>Ensino > Meus Dados > Dados Pessoais</strong>.</li>
        <li>Na ultima linha da tabela de <strong>Dados Gerais</strong> procure pela <strong>Chave de Acesso</strong> (Vai ser algo parecido com 5e8h9).</li>
        <li>Copie ou anote a sua chave de acesso.</li>
    </ol>

    <p>Em seguida, basta preencher o formulário abaixo:</p>

    @if(Session::has('success_message'))
    <div class="alert alert-dismissible alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('success_message') }}
    </div>
    @endif

    @if(Session::has('info_message'))
    <div class="alert alert-dismissible alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('info_message') }}
    </div>
    @endif

    @if(Session::has('warning_message'))
    <div class="alert alert-dismissible alert-warning">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('warning_message') }}
    </div>
    @endif

    @if(Session::has('danger_message'))
    <div class="alert alert-dismissible alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ Session::get('danger_message') }}
    </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-warning pb-0">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ action('SUAPBotController@postAuth', $user->telegram_id) }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <input type="text" name="suapid" value="{{ old('suapid') }}" placeholder="Matrícula no SUAP" class="form-control col-md-3">
        </div>
        <div class="form-group">
            <input type="text" name="suapkey" value="{{ old('accesskey') }}" placeholder="Chave de acesso" class="form-control col-md-3">
        </div>
        <div class="form-group">
            <button type="submit" name="button" class="btn btn-primary">Enviar</button>
        </div>
    </form>
</div>
@endsection
