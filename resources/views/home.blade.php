@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Users</div>

                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Telegram ID</th>
                                <th>Email</th>
                                <th>Notify</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->telegram_id }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->notify)
                                    <span class="label">on</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

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
