@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <h2>Users ({{ $users->count() }})</h2>
            <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#broadcastMessageModal">
                Broadcast Message
            </button>
            <hr>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Username</th>
                        <th>Telegram ID</th>
                        <th>Email</th>
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


        </div>

    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="broadcastMessageModal" tabindex="-1" role="dialog" aria-labelledby="broadcastMessageModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ url('home') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="broadcastMessageModalLabel">Broadcast Message</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea name="message" rows="8" placeholder="Message to broadcast..." class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="button" class="btn btn-info pull-right">Send!</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
