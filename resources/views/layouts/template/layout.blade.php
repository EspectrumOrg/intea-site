<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel INTEA (Beta) - Dashboard</title>
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}"> <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}"> <!-- profile -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}"> <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/popular.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
</head>

<body>
    <div class="layout">
        <!-- conteúdo navbar  -->
        <div class="container-content-nav">
            @include("layouts.partials.menu")
        </div>

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
                @include('dashboard.post.partials.sidebar-popular')
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('js/posts/mostrar-mais.js') }}"></script>

</html>