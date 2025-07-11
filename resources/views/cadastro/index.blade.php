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
    <header class="header">
        <img src="{{ asset('assets/images/logos/intea/logo.png') }}" alt="Logo" class="logo">
    </header>

    <main class="container">
        <section class="form">
            <h1>Escolha o tipo de conta</h1>
            <p>Selecione uma das opções abaixo para continuar com o cadastro:</p>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.autista') }}">Autista</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.comunidade') }}">Comunidade</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.responsavel') }}">Responsavel</a></p>
            </button>

            <button class="botao-opcao">
                <a href="{{ route('cadastro.profissionalsaude') }}">Profissional da Saúde</a></p>
            </button>

            <div class="registro">
                <p>Já possui uma conta? <a href="{{ route('index') }}">Entre aqui</a></p>
            </div>
        </section>

    </main>
</body>

</html>