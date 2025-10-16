<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chat')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style2.css') }}">
    
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>
<body>



    <div class="container">
        @yield('main')
    </div>

    @yield('scripts')
</body>
</html>
