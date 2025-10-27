<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel INTEA - geral</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}"> <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}"> <!-- profile -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}"> <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/create-postagem.css') }}"> 
    <link rel="stylesheet" href="{{ asset('assets/css/post/edit-modal.css') }}"> 
    <link rel="stylesheet" href="{{ asset('assets/css/post/topo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/postagem-modal.css') }}">
</head>

<body>
    <div class="layout">
        <!-- conteúdo navbar 
        <div class="container-content-nav">
            @include("layouts.partials.menu")
        </div> -->
        WRONG
        <div class="container-content">
            <!-- conteúdo sidebar  -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>
        WRONG

            <!-- conteúdo principal  -->
            <div class="container-main">
                @yield("main")
        WRONG
            </div>

            <!-- conteúdo popular  -->
            <div class="content-popular">
                
                @include('feed.post.partials.sidebar-popular')
            </div>
        WRONG
        </div>
    </div>
</body>

<!-- postagens  -->
<script src="{{ url('assets/js/posts/mostrar-mais.js') }}"></script>
<script src="{{ url('assets/js/posts/carregar-comentarios-post.js') }}"></script>
<script src="{{ url('assets/js/posts/modal-denuncia.js') }}"></script>
<script src="{{ url('assets/js/posts/modal-edit.js') }}"></script>

<!-- sidebar links (esquerda)  -->
<script src="{{ url('assets/js/sidebar/modal-postagem.js') }}"></script>

</html>