<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/inicio/welcome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <header class="header">
        <img src="{{ asset('assets/images/logos/intea/logo.png') }}" alt="Logo" class="logo">
    </header>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <main class="container">
        <form action="" method="POST" class="form">
            @csrf

            <h1>Entrar</h1>
            <p>Preencha seus dados para acessar a plataforma</p>

            <div class="input-box">
                <input type="email" name="email" required>
                <label>Email</label>
                <i class="fas fa-envelope icon"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" required>
                <label>Senha</label>
                <i class="fas fa-lock icon"></i>
            </div>

            <div class="esqueci-senha">
                <a href="#">Esqueci minha senha</a>
            </div>

            <button type="submit" class="btn-login">Entrar</button>

            <div class="ou">
                <hr>
                <h2> ou </h2>
                <hr>
            </div>

            <div class="registro">
                <p>Não tem uma conta? Registre-se aqui</p>
            </div>

            <button type="button" class="botao-registro">
                <a href="{{ route('cadastro.index') }}">Criar Conta</a>
            </button>


        </form>
    </main>
</body>

</html>