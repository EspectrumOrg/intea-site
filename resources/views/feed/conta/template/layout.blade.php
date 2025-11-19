<!doctype html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Intea - conta</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}"> <!-- layout geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/conta/style.css') }}"> <!-- conta -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}"> <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/create/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/update/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
</head>

<body>
    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar  -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- conteúdo principal  -->
            <div class="container-main">
                @yield("main")
            </div>

            <!-- conteúdo você pode gostar  -->
            <div class="content-popular">
                @include('feed.post.partials.buscar')
                @include('feed.post.partials.sidebar-popular')
            </div>
        </div>
    </div>
</body>

<!-- conta  -->
<script src="{{ url('assets/js/feed/conta/modal-denuncia-usuario.js') }}"></script>
<script src="{{ url('assets/js/feed/conta/modal-images.js') }}"></script>

<!-- postagens  -->
<script src="{{ url('assets/js/posts/carregar-comentarios-post.js') }}"></script>
<script src="{{ url('assets/js/posts/modal-denuncia.js') }}"></script>
<script src="{{ url('assets/js/posts/mostrar-mais.js') }}"></script>
<script src="{{ url('assets/js/posts/create/modal.js') }}"></script>
<script src="{{ url('assets/js/posts/create/char-count.js') }}"></script>
<script src="{{ url('assets/js/posts/update/modal.js') }}"></script>

</html>