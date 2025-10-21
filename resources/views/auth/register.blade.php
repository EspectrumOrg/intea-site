<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - tipo conta</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/cadastro.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body>
    <a href="{{ route('login') }}">
        <div class="register-container">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
    </a>
    <div class="register-content">
        <img class="logo-y" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
        <div class="options">
            <div class="descricao">
                <h2>Escolha o tipo de conta</h2>
                <p id="descricao">Selecione uma das opções abaixo:</p>
            </div>


            <!-- create autista -->

<div class="btn-center">
 <a href="{{ route('autista.create') }}">
                <button class="contact1-form-btn" type="submit" class="btn-primary">
                    Autista
                </button>
</a>
        <!-- create comunidade-->

    <a href="{{ route('comunidade.create') }}">
            <button class="contact1-form-btn" type="submit" class="btn-primary">
                Comunidade
            </button>
</a>

    <!-- create profissional
                <div class="option-group">
                    <a href="{{ route('profissional.create') }}"><button class="botao-opcao">Profissional Saúde</button></a>
                </div>
                -->

    <!-- create responsavel -->

    <a href="{{ route('responsavel.create') }}">
        <button class="contact1-form-btn" type="submit" class="btn-primary">
            Responsavel
        </button>
    </a>
</div>

    <!-- Login -->
    <div class="registro">
        <p>Já possui uma conta? <a href="{{ route('login') }}">Entre aqui</a></p>
    </div>
    </div>
    </div>

    </div>
</body>

</html>