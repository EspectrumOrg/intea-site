<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Tipo de Conta</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cadastro/style.css') }}">
</head>

<body>

    <div class="register-container">

        <div class="options">
            <div class="descricao">
                <h2>Escolha o tipo de conta</h2>
                <p id="descricao">Selecione uma das opções abaixo:</p>
            </div>


            <!-- create autista -->
            <div class="option-group">
                <a href="{{ route('cadastro.autista') }}"><button class="botao-opcao">Autista</button></a>
            </div>

            <!-- create comunidade-->
            <div class="option-group">
                <a href="{{ route('cadastro.comunidade') }}"><button class="botao-opcao">Comunidade</button></a>
            </div>

            <!-- create profissional-->
            <div class="option-group">
                <a href="{{ route('cadastro.profissionalsaude') }}"><button class="botao-opcao">Profissional Saúde</button></a>
            </div>

            <!-- create responsavel -->
            <div class="option-group">
                <a href="{{ route('cadastro.responsavel') }}"><button class="botao-opcao">Responsável</button></a>
            </div>

            <!-- Login -->
            <div class="registro">
                <p>Já possui uma conta? <a href="{{ route('welcome') }}">Entre aqui</a></p>
            </div>
        </div>

    </div>
</body>

</html>