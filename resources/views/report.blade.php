@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Message report</div>

                <div class="panel-body">
                    <h4>Message:</h4>
                    <div class="form-group">
                        <textarea rows="8" class="form-control" disabled>{{ $message }}</textarea>
                    </div>

                    <h4>Message sent to:</h4>
                    <ul>
                        @foreach($sent as $user)
                        <li>{{ $user->telegram_id }} - {{ $user->first_name }} {{ $user->last_name }} {{ $user->username ? '@'.$user->username : '' }}</li>
                        @endforeach
                    </ul>

                    <h4>Couldn't send to:</h4>
                    <ul>
                        @foreach($not_sent as $data)
                        <li>{{ $data[0]->telegram_id }} - {{ $data[0]->first_name }} {{ $data[0]->last_name }} {{ $data[0]->username ? '@'.$data[0]->username : '' }} ({{ $data[1] }})</li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
