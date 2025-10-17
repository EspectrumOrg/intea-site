<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>intea - Login</title>

    <!-- Link do CSS -->
    <link rel="stylesheet" href="{{ url('assets/css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 ">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="login-container">
                <div class="login-container">
                    <a href="{{ route('landpage') }}">
                        <span class="material-symbols-outlined">
                            arrow_back
                        </span>
                    </a>
                    @if (session('status'))
                        <p class="session-status">{{ session('status') }}</p>
                    @endif

                    <div class="login-content">
                        <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp.png') }}">
                        <form method="POST" action="{{ route('login') }}" class="login-form">
                            @csrf

                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username">
                                @if ($errors->has('email'))
                                    <p class="error">{{ $errors->first('email') }}</p>
                                @endif
                            </div>

                            <!-- Senha -->
                            <div class="form-group">
                                <label for="password">Senha</label>
                                <div class="password-wrapper">
                                    <input id="password" type="password" name="password" required
                                        autocomplete="current-password">

                                    <img id="btnPassword" src="{{ asset('assets/images/logos/symbols/view-open.png') }}"
                                        data-open="{{ asset('assets/images/logos/symbols/view-open.png') }}"
                                        data-close="{{ asset('assets/images/logos/symbols/view-close.png') }}"
                                        alt="mostrar senha" onclick="visualizarSenha()" />
                                </div>


                                @if ($errors->has('password'))
                                    <p class="error">{{ $errors->first('password') }}</p>
                                @endif
                            </div>

                            <div class="beto">
                                <div class="form-actions">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-senha">Esqueceu sua
                                            senha?</a>
                                    @endif
                                </div>

                                <!-- Ações -->

                                <div class="container-contact1-form-btn">
                                    <button class="contact1-form-btn" type="submit" class="btn-primary">
                                        Entrar
                                    </button>
                                </div>
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
            </div>
        </div>
</body>

<script src="{{ asset('assets/js/auth/visualizar-senha.js') }}"></script>

</html>