@extends('layouts.app')

@section('content')
<div class="ui container">
    <h2>Your message</h2>

    <div class="ui message">
        {{ $message }}
    </div>

    <h2>Sent to {{ count($sent) }} {{ str_plural('user', count($sent)) }}.</h2>
    <table class="ui celled table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Notify</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sent as $user)
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
                <td>
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                </td>
                <td>
                    @if($user->suap_id)
                        @if($user->notify)
                        <i class="large green checkmark icon"></i>
                        @else
                        <i class="large red close icon"></i>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(! empty($not_sent))
    <h2>Not sent to {{ count($not_sent) }} {{ str_plural('user', count($not_sent)) }}.</h2>
    <table class="ui celled table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Notify</th>
                <th>Error</th>
            </tr>
        </thead>
        <tbody>
            @foreach($not_sent as $data)
            <tr>
                <td>{{ $data[0]->id }}</td>
                <td>
                    <h4 class="ui image header">
                        <img src="https://gravatar.com/avatar/{{ md5(strtolower(trim($data[0]->email))) }}" class="ui mini rounded image">
                        <div class="content">
                            {{ $data[0]->first_name }} {{ $data[0]->last_name }}
                            <div class="sub header"><a href="{{ $data[0]->username ? 'https://telegram.me/'.$data[0]->username : '#' }}" target="_blank">{{ $data[0]->username ? '@'.$data[0]->username : '' }}</a>
                            </div>
                        </div>
                    </h4>
                </td>
                <td>
                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                </td>
                <td>
                    @if($user->suap_id)
                        @if($user->notify)
                        <i class="large green checkmark icon"></i>
                        @else
                        <i class="large red close icon"></i>
                        @endif
                    @endif
                </td>
                <td>{{ $data[1] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
