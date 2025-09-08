<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - tipo conta</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/cadastro.css') }}">
</head>

<body>
    <div class="register-container">
        <a class="voltar" href="{{ route('landpage') }}"><img src="{{ asset('assets/images/logos/symbols/back-button.png') }}"></a>

        <div class="register-content">
            <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
            <div class="options">
                <div class="descricao">
                    <h2>Escolha o tipo de conta</h2>
                    <p id="descricao">Selecione uma das opções abaixo:</p>
                </div>


                <!-- create autista -->
                <div class="option-group">
                    <a href="{{ route('autista.create') }}"><button class="botao-opcao">Autista</button></a>
                </div>

                <!-- create comunidade-->
                <div class="option-group">
                    <a href="{{ route('comunidade.create') }}"><button class="botao-opcao">Comunidade</button></a>
                </div>

                <!-- create profissional-->
                <div class="option-group">
                    <a href="{{ route('profissional.create') }}"><button class="botao-opcao">Profissional Saúde</button></a>
                </div>

                <!-- create responsavel -->
                <div class="option-group">
                    <a href="{{ route('responsavel.create') }}"><button class="botao-opcao">Responsável</button></a>
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