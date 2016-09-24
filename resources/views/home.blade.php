@extends('layouts.app')

@section('content')
    <div class="ui container">

        <div class="ui four statistics">
            <div class="statistic">
                <div class="value">
                    {{ $stats['total'] }}
                </div>
                <div class="label">
                    Users
                </div>
            </div>
            <div class="statistic">
                <div class="value">
                    {{ $stats['active'] }}
                </div>
                <div class="label">
                    Active Users
                </div>
            </div>
            <div class="statistic">
                <div class="value">
                    {{ $stats['today'] }}
                </div>
                <div class="label">
                    New Today
                </div>
            </div>
            <div class="statistic">
                <div class="value">
                    {{ $stats['week'] }}
                </div>
                <div class="label">
                    New This Week
                </div>
            </div>
        </div>

        <table class="ui celled table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Not.</th>
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

        <button class="ui right floated right labeled red icon broadcast button">
            <i class="right announcement icon"></i>
            Broadcast Message
        </button>
    </div>
@endsection

@section('modals')
    <div class="ui broadcast modal">
        <div class="header">
            New Broadcast
        </div>
        <div class="content">
            <form action="{{ url('home') }}" method="post" class="ui form">
                {{ csrf_field() }}

                <div class="field">
                    <textarea name="message" rows="8" placeholder="Message to broadcast..." class="form-control"></textarea>
                </div>
            </div>
            <div class="actions">
                <button class="ui positive right labeled icon button" role="submit">
                    Send
                    <i class="send icon"></i>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    $('.broadcast.button').on('click', function(){
        $('.ui.broadcast.modal').modal('show');
    });
    </script>
@endsection
