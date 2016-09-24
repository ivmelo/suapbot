@extends('layouts.app')

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <form class="ui large form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}
            <div class="ui segment">
                <h2 class="ui image header">
                    <!-- <img src="assets/images/logo.png" class="image"> -->
                    <div class="content">
                        Log-in to SUAP Bot
                    </div>
                </h2>

                @if (count($errors) > 0)
                <div class="ui negative message">
                    <i class="close icon"></i>
                    <div class="header">
                        Ops...
                    </div>
                    <ul class="list">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="field {{ $errors->has('email') ? 'error' : '' }}">
                    <div class="ui left icon input">
                        <i class="user icon"></i>
                        <input type="text" name="email" placeholder="E-mail address" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="field{{ $errors->has('password') ? ' error' : '' }}">
                    <div class="ui left icon input">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="Password">
                    </div>
                </div>
                <button type="submit" class="ui fluid large green submit button">Login</button>
            </div>

            <div class="ui error message"></div>

        </form>
    </div>
</div>
@endsection

@section('styles')
<style media="screen">
    body > .grid {
      height: 100%;
    }
    .image {
      margin-top: -100px;
    }
    .column {
      max-width: 450px;
    }
</style>
@endsection

@section('scripts')
<script>
$('.message .close')
.on('click', function() {
    $(this)
    .closest('.message')
    .transition('fade')
    ;
})
;
</script>
@endsection
