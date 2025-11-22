<!doctype html>
<html lang="pt-br">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Intea - Home</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/modal-template.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/update/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}">
    <!-- interesses -->
    <link rel="stylesheet" href="{{ asset('assets/css/interesses.css') }}">
    <!-- read -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/postagem-read/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feed/postagem-read/create-comentario-read.css') }}">
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

            <!-- barra de pesquisa, popular  -->
            <div class="content-popular">
                @include('feed.post.partials.buscar')
                @include('feed.post.partials.sidebar-popular')
            </div>
        </div>

        <!-- Modal Criação de postagem -->
        @include('feed.post.create-modal')

        <!-- modal de avisos -->
        @include("layouts.partials.avisos")
    </div>
</body>

<!-- postagens  -->
<script src="{{ url('assets/js/posts/carregar-comentarios-post.js') }}"></script>
<script src="{{ url('assets/js/posts/mostrar-mais.js') }}"></script>
<script src="{{ url('assets/js/posts/modal-denuncia.js') }}"></script>

<!-- read comentários -->
<script src="{{ url('assets/js/posts/read/char-count.js') }}"></script>
<script src="{{ url('assets/js/posts/read/hashtag-comentario-read.js') }}"></script>
<script src="{{ url('assets/js/posts/read/create-resposta-comentario-focus.js') }}"></script>

<script src="{{ url('assets/js/posts/comentario/modal-denuncia-comentario.js') }}"></script>
<script src="{{ url('assets/js/posts/read/char-count-focus.js') }}"></script>
<!-- postagen dropdown -->
<script src="{{ url('assets/js/posts/dropdown-option.js') }}"></script>

<!-- interesses -->
<script src="{{ url('assets/js/interesses/seguir-interesse.js') }}"></script>
<script src="{{ url('assets/js/interesses/sidebar-interesses.js') }}"></script>
<script src="{{ asset('assets/js/posts/selecao-interesse.js') }}"></script>

<!-- Moderação -->
 <script src="{{ asset('assets/js/moderacao/error-handler.js') }}"></script>

</html>