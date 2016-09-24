<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SUAP Bot</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.css">

    <style media="screen">
    body {
        padding-top: 4em;
    }
    </style>

    @yield('styles')
</head>
<body>
    <div class="ui inverted fixed menu">
        <div class="ui container">
            <a class="header item" href="{{ url('/') }}">
                SUAP Bot
            </a>

            @if(Auth::user())
                <a href="{{ url('users') }}" class="item">
                    Users
                </a>

                <div class="ui simple right dropdown item">
                    {{ Auth::user()->first_name }}
                    <span class="dropdown icon"></span>
                    <div class="menu">
                        <a href="{{ url('logout') }}" class="item">Logout</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @yield('content')
    @yield('modals')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.js"></script>
    @yield('scripts')
</body>
</html>
