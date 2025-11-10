<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Grupos</title>
    <!-- css geral -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}">
    <!-- css grupos -->
    <link rel="stylesheet" href="{{ asset('assets/css/feed/grupos/style.css') }}">
</head>

<body>

    <div class="layout">
        <div class="container-content">
            <!-- conteúdo sidebar  -->
            <div class="container-sidebar">
                @include("layouts.partials.sidebar")
            </div>

            <!-- conteúdo principal  -->
            <div class="container-main estilo-geral">
                @yield("main")
            </div>

            <!-- conteúdo popular  -->
            <div class="content-popular">
            @include('feed.post.partials.buscar')

            @include('feed.post.partials.sidebar-popular')
            </div>
        </div>
    </div>

</body>

</html>