@extends('layouts.app')

@section('content')


<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal image header">
            <img src="assets/images/logo.png" class="image">
            <div class="content">
                Log-in to your account
            </div>
        </h2>
        <form class="ui large form">
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="email" placeholder="E-mail address">
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="ui fluid large teal submit button">Login</div>
            </div>

            <div class="ui error message"></div>

        </form>

        <div class="ui message">
            New to us? <a href="#">Sign Up</a>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-md-3">
            <form action="{{ url('continue') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="text" name="ifrn_id" value="{{ $std_data['matricula'] }}" placeholder="matricula" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <input type="text" name="name" value="{{ $std_data['nome'] }}" placeholder="nome" class="form-control" disabled>
                </div>
                <div class="form-group">
                    <input type="email" name="email" value="{{ $std_data['email_pessoal'] }}" placeholder="email" class="form-control">
                </div>
                <div class="form-grop">
                    <button type="submit" name="button" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
        <div class="col-md-9">
            <div class="list-group">
                @foreach($course_data as $course)
                <a href="#" class="list-group-item">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="list-group-item-heading">{{ $course['disciplina'] }}</h4>
                            <p class="list-group-item-text">{{ $course['codigo'] }} | diário {{ $course['diario'] }} | {{ $course['carga_horaria'] }} aulas | {{ $course['situacao'] }}</p>
                        </div>
                        <div class="col-md-2">
                            <h3 style="margin: .3em" class="text-center">{{ $course['frequencia'] }}%</h3>
                        </div>
                        <div class="col-md-1">
                            <h3 style="margin: .3em" class="text-center">{{ $course['bm1_nota'] }}</h3>
                        </div>
                        <div class="col-md-1">
                            <h3 style="margin: .3em" class="text-center">{{ $course['bm2_nota'] }}</h3>
                        </div>
                        <div class="col-md-2">
                            <h3 style="margin: .3em" class="text-center">{{ $course['mfd'] }}</h3>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection
