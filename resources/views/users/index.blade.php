@extends('layouts.app')

@section('content')

<div class="ui container">
    <table class="ui celled table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>First Name</th>
                <th>Telegram #ID</th>
                <th>Email</th>
                <th>Notify</th>
                <th>Created</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>
                    <h4 class="ui image header">
                        <img src="https://gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}" class="ui mini rounded image">
                        <div class="content">
                            {{ $user->first_name }} {{ $user->last_name }}
                            <div class="sub header"><a href="{{ $user->username ? 'https://telegram.me/'.$user->username : '#' }}" target="_blank">{{ $user->username ? '@'.$user->username : '' }}</a>
                            </div>
                        </div>
                    </h4>
                </td>
                <td>{{ $user->telegram_id }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->suap_id)
                        @if($user->notify)
                        <i class="large green checkmark icon"></i>
                        @else
                        <i class="large red close icon"></i>
                        @endif
                    @endif
                </td>
                <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
