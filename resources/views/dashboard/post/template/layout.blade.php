<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel INTEA (Beta) - Postagens</title>
    <link rel="stylesheet" href="{{ url('assets/css/layout/layout.css') }}"> <!-- layout geral -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/navbar.css') }}"> <!-- navbar -->
    <link rel="stylesheet" href="{{ url('assets/css/layout/sidebar.css') }}"> <!-- sidebar esquerda -->
    <link rel="stylesheet" href="{{ url('assets/css/profile/style.css') }}"> <!-- profile -->
    <link rel="stylesheet" href="{{ asset('assets/css/post/style.css') }}"> <!-- postagens -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> <!-- css geral -->
</head>

<body>

    <div class="container-content">
            @include('dashboard.post.create')
            <!-- conteúdo principal  -->
            <div class="container-main">
                @yield('main')
            </div>

            <!-- o que está bombando (mais vistos) -->
            <div class="container-popular">
                @include('dashboard.post.partials.sidebar-popular')
            </div>

        </div>
    </div>
    
</body>

</html>