<!DOCTYPE html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Chat')</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/chats/estilo-chat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/style.css') }}">
    <!-- Postagem -->
    <link rel="stylesheet" href="{{ asset('assets/css/profile/postagem.css') }}">

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
</head>

<body class="{{ auth()->user()->tema_preferencia === 'monocromatico' ? 'monochrome' : '' }}">
    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar  -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <div class="container container-main">
                @yield('main')
            </div>
        </div>
    </div>

    <!-- Modal Criação de postagem -->
    @include('feed.post.create-modal')
    
    @yield('scripts')
</body>

</html>