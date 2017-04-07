@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2>Login</h2>
            <form class="ui large form" method="POST" action="{{ url('/login') }}">
                {{ csrf_field() }}

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">@</span>
                        <input type="text" class="form-control" placeholder="Email" aria-describedby="basic-addon1" name="email" placeholder="E-mail address" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon2">@</span>
                        <input type="password" name="password" class="form-control" placeholder="Password" aria-describedby="basic-addon2">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
