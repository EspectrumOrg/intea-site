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
    <div class="register-container">
        <a href="{{ route('login') }}" class="voltar">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
        </a>
        <div class="register-content">

            <div class="container-logo">
                <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
            </div>

            <div class="options">
                <div class="descricao">
                    <h2>Escolha o tipo de conta</h2>
                    <p id="descricao">Selecione uma das opções abaixo:</p>
                </div>


                <div class="btn-center">
                    <!-- create autista -->
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