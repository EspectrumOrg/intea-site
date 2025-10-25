<!doctype html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Intea - Feed</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}">
    <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/update/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
    <!-- comentario -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/comentario/modal.css') }}">
    <!-- read -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/postagem-read/modal.css') }}">
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

            <!-- conteúdo popular  -->
            <div class="content-popular">
                @include('feed.post.partials.sidebar-popular')
            </div>
        </div>

        
        <!-- Modal Criação de postagem -->
        @include('feed.post.create-modal')

        <!-- modal de avisos 
        @include("layouts.partials.avisos")-->
    </div>
</body>

<!-- postagens  -->
<script src="{{ url('assets/js/posts/carregar-comentarios-post.js') }}"></script>
<script src="{{ url('assets/js/posts/modal-denuncia.js') }}"></script>
<script src="{{ url('assets/js/posts/mostrar-mais.js') }}"></script>
<script src="{{ url('assets/js/posts/create/char-count.js') }}"></script>
<script src="{{ url('assets/js/posts/update/modal-update.js') }}"></script>

<!-- comentario -->
<script src="{{ url('assets/js/posts/comentario/modal.js') }}"></script>

<!-- read comentários -->
<script src="{{ url('assets/js/posts/read/char-count.js') }}"></script>

<!-- postagen dropdown -->
<script src="{{ url('assets/js/posts/dropdown-option.js') }}"></script>

</html>