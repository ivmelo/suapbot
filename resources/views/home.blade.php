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
                <th>First Name</th>
                <!-- <th>Username</th> -->
                <th>Telegram #ID</th>
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

@section('modals')
<!-- <div class="modal fade" id="broadcastMessageModal" tabindex="-1" role="dialog" aria-labelledby="broadcastMessageModalLabel">
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
</div> -->

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
        <button class="ui black deny button">
            Cancel
        </button>
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
$('.ui.broadcast.modal')
  .modal('show')
;
</script>
@endsection
