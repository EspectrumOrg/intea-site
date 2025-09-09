<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Intea - cadastro</title>
    <link rel="stylesheet" href="{{ asset('assets/css/auth/cadastro.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- parte form-outer e form -->
    <a href="{{ route('register') }}"><img class="logo-cadastro" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}"></a>
    
    <main class="container-cadastro">
        @yield("main")

        <!-- Links -->
        <div class="voltar">
            <p><a href="{{ route('register') }}">Tipo conta</a></p>
        </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/auth/cadastro.js') }}"></script>
    <script src="{{ asset('assets/js/auth/progresso.js') }}"></script>
    <script src="{{ asset('assets/js/auth/registro_profissional_saude.js') }}"></script>
</body>

</html>