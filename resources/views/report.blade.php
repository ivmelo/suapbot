@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Broadcast Results</h2>

    <div class="form-group">
        <textarea class="form-control" rows="8" readonly>{{ $message }}</textarea>
    </div>

    <p>Your message was sent to {{ count($sent) }} {{ str_plural('user', count($sent)) }}.</p>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Gravatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Not.</th>
                <th>Created</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sent as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <img src="https://gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}" class="gravatar">
                    </td>
                    <td>
                        {{ $user->first_name }} {{ $user->last_name }}
                        <div class="sub header"><a href="{{ $user->username ? 'https://telegram.me/'.$user->username : '#' }}" target="_blank">{{ $user->username ? '@'.$user->username : '' }}</a></div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->suap_id)
                            @if($user->notify)
                                <span class="led on"></span>
                            @else
                                <span class="led off"></span>
                            @endif
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(! empty($not_sent))
    <p>Not sent to {{ count($not_sent) }} {{ str_plural('user', count($not_sent)) }}.</p>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Gravatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Not.</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @foreach($not_sent as $data)
                <tr>
                    <td>{{ $data['user']->id }}</td>
                    <td>
                        <img src="https://gravatar.com/avatar/{{ md5(strtolower(trim($data['user']->email))) }}" class="gravatar">
                    </td>
                    <td>
                        {{ $data['user']->first_name }} {{ $data['user']->last_name }}
                        <div class="sub header"><a href="{{ $data['user']->username ? 'https://telegram.me/'.$data['user']->username : '#' }}" target="_blank">{{ $data['user']->username ? '@'.$data['user']->username : '' }}</a></div>
                    </td>
                    <td>{{ $data['user']->email }}</td>
                    <td>
                        @if($data['user']->suap_id)
                            @if($data['user']->notify)
                                <span class="led on"></span>
                            @else
                                <span class="led off"></span>
                            @endif
                        @endif
                    </td>
                    <td>{{ $data['message'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
