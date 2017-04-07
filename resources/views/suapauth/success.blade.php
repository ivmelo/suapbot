@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tudo certo!</h3>
    <p>Suas credenciais foram validadas com sucesso!</p>
    <p>Agora você já pode usar o SUAP Bot.</p>

    <a href="https://telegram.me/{{ env('TELEGRAM_BOT_HANDLE') }}" class="btn btn-success">Voltar para o SUAP Bot</a>
</div>
@endsection
