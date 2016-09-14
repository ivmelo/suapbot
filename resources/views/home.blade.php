@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Users ({{ $users->count() }})</div>

                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Username</th>
                                <th>Telegram ID</th>
                                <th>Email</th>
                                <th>SUAP ID</th>
                                <th>Ntf</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                    @if($user->is_admin)
                                    <span class="label label-success">Admin</span>
                                    @endif
                                </td>
                                <td><a href="{{ $user->username ? 'https://telegram.me/'.$user->username : '#' }}" target="_blank">{{ $user->username ? '@'.$user->username : '' }}</a></td>
                                <td>{{ $user->telegram_id }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->suap_id }}</td>
                                <td>
                                    @if($user->notify)
                                    <span class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>
                                    @else
                                    <span class="label label-danger"><span class="glyphicon glyphicon-remove"></span></span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <h4>Broadcast message</h4>
                    <form action="{{ url('home') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <textarea name="message" rows="8" placeholder="Message goes here..." class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="button" class="btn btn-info pull-right">Send!</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
