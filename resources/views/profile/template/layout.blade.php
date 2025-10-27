<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Intea - Perfil</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}"> <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}"> <!-- profile -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/post/create/style.css') }}"> 
    <link rel="stylesheet" href="{{ asset('assets/css/post/create/modal.css') }}"> 
    <link rel="stylesheet" href="{{ asset('assets/css/post/update/modal.css') }}"> 
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
                @include('profile.partials.buscar') 
                @include('feed.post.partials.sidebar-popular')
            </div>
        </div>
    </div>
</body>

<!-- sidebar links (esquerda)  -->
<script src="{{ url('assets/js/posts/create/modal.js') }}"></script>

</html>