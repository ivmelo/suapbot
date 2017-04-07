@extends('layouts.app')

@section('content')
    <div class="container">

        {{-- <div class="ui four statistics">
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
        </div> --}}

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
                @foreach($users as $user)
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

        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#broadcastModal">
          Broadcast Message
        </button>
    </div>
@endsection

@section('modals')
<div class="modal fade" id="broadcastModal" tabindex="-1" role="dialog" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ url('home') }}" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="broadcastModalLabel">Broadcast Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="message" rows="8" placeholder="Message to broadcast..." class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send to Everyone</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
