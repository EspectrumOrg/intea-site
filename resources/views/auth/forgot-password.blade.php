<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Esqueci Senha</title>
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/esqueceu-senha.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="http://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

</head>

<body>
    <!-- Voltar -->
    <div class="voltar">
        <a href="{{ route('login') }}">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    </div>


    <!-- Logo -->
    <div class="logo-container">
        <a href="{{ route('login') }}">
            <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp-cadastro.png') }}">
        </a>
    </div>


    <div class="container">

        <!-- Form correto para reset de senha -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <h2>Esqueci minha senha</h2>
            <p class="texto-esqueceu">
                Você quer redefinir sua senha? Sem problemas, nos informe seu e-mail para que possamos te mandar uma verificação no seu e-mail
            </p>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Botão -->
            <div class="btn-submit">
                <button type="submit">Enviar</button>
            </div>

            <!-- Mostra status do envio -->
            @if (session('status'))
            <p class="session-status">{{ session('status') }}</p>
            <script>
                console.log("{{ session('status') }}");
            </script>
            @endif

        </form>
    </div>

    <script src="{{ asset('assets/js/auth/visualizar-senha.js') }}"></script>
</body>

</html>