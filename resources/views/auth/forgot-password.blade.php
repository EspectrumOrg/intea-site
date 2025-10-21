<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="http://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/esqueceu-senha.css') }}">
</head>

<body>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
    </form>

    <div class="login-container">
        <a href="{{ route('login') }}" class="voltar">
            <span class="material-symbols-outlined">
                arrow_back
            </span>
        </a>

        @if (session('status'))
        <p class="session-status">{{ session('status') }}</p>
        @endif

        <div class="login-content">
            <div class="container-logo">
                <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
            </div>

            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf
                <h2>Esqueci minha senha</h2>

                <p class="texto-esqueceu">Você quer redefinir sua senha? Sem problemas, nos informe seu e-mail para que
                    possamos te mandar uma verificação no seu e-mail </p>


                <!-- Email -->
                <div class="form-group">
                    <label for="email" :value="__('Email')">Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                        autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Ações -->
                <div class="container-contact1-form-btn">
                    <button class="contact1-form-btn" type="submit" class="btn-primary">
                        Enviar verificação
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script src="{{ asset('assets/js/auth/visualizar-senha.js') }}"></script>

</html>

</body>

</html>