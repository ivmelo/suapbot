@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ url('suap') }}" method="post">
        {{ csrf_field() }}
        <div class="form-group">
            <input type="text" name="ifrn_id" value="20121014040083" placeholder="matricula">
        </div>
        <div class="form-group">
            <input type="password" name="access_key" value="2d3d9" placeholder="chave de acesso">
        </div>
        <div class="form-grop">
            <button type="submit" name="button">Enviar</button>
        </div>
    </form>
</div>
@endsection
