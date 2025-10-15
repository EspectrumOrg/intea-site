<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Esqueci a senha</title>
<link rel="stylesheet" href="{{ asset('assets/css/auth/esqueceu-senha.css') }}"> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="http://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">
</head>
<body>
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail e enviaremos um link para redefinição de senha que permitirá que você escolha uma nova.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Link de redefinição de senha de e-mail') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

</body>
</html>















<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>intea - Login</title>

    <!-- Link do CSS -->
    <link rel="stylesheet" href="{{ url('assets/css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
</head>

<body>
    <div class="login-container">
        <a class="voltar" href="{{ route('login') }}"><img src="{{ asset('assets/images/logos/symbols/back-button.png') }}"></a>

        @if (session('status'))
        <p class="session-status">{{ session('status') }}</p>
        @endif

        <div class="login-content">
            <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
            <form method="POST" action="{{ route('login') }}" class="login-form">
                @csrf

                <h2>Entrar</h2>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @if ($errors->has('email'))
                    <p class="error">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Senha -->
                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="password-wrapper">
                        <input id="password" type="password" name="password" required autocomplete="current-password">

                        <img
                            id="btnPassword"
                            src="{{ asset('assets/images/logos/symbols/view-open.png') }}"
                            data-open="{{ asset('assets/images/logos/symbols/view-open.png') }}"
                            data-close="{{ asset('assets/images/logos/symbols/view-close.png') }}"
                            alt="mostrar senha"
                            onclick="visualizarSenha()" />
                    </div>


                    @if ($errors->has('password'))
                    <p class="error">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Lembrar-me -->
                <div class="form-group remember-me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Lembrar-me</label>
                </div>

                <!-- Ações -->
                <div class="form-actions">
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
                    @endif

                    <button type="submit" class="btn-primary">Entrar</button>
                </div>

                <!-- Criar conta -->
                <div class="register-link">
                    <p>Não tem conta? <a href="{{ route('register') }}">Criar conta</a></p>
                </div>
            </form>
        </div>

        <!-- Modal de Erro -->
        @if(session('conta_status'))
        <div id="loginModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <p>{{ session('conta_status') }}</p>
            </div>
        </div>
        @endif

        <script>
            function closeModal() {
                document.getElementById('loginModal').style.display = 'none';
            }
        </script>

        
    </div>
</body>

<script src="{{ asset('assets/js/auth/visualizar-senha.js') }}"></script>

</html>