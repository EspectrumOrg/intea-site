<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Link do CSS -->
    <link rel="stylesheet" href="{{ url('assets/css/inicio/login.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
</head>

<body>
    <div class="login-container">
        @if (session('status'))
        <p class="session-status">{{ session('status') }}</p>
        @endif

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
                <input id="password" type="password" name="password" required autocomplete="password">
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
</body>

</html>