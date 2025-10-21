<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - cadastro</title>
    <link rel="stylesheet" href="{{ asset('assets/css/auth/cadastro.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body>

    <!-- parte form-outer e form -->
    <main class="container-cadastro">
        @yield("main")

        <!-- Links -->
        <div class="voltar">
            <a href="{{ route('register') }}">
                <span class="material-symbols-outlined">
                arrow_back
                </span>
            </a>
        </div>
    </main>

    <!-- Js-->
    <script src="{{ asset('assets/js/auth/cadastro.js') }}"></script>
    <script src="{{ asset('assets/js/auth/progresso.js') }}"></script>
    <script src="{{ asset('assets/js/auth/validacao.js') }}"></script>
    <script src="{{ asset('assets/js/auth/registro_profissional_saude.js') }}"></script>

    <!-- JQuery-->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Máscara-->
    <script>
        $('.telefone-input').mask('(00) 00000-0000')
        $('.cpf-input').mask('000.000.000-00')
        $('.user-input').mask('@AAAAAAAAAAAAAAAAAAAAAAAA', {
            translation: {
                'A': {
                    pattern: /[a-zA-Z0-9]/,
                    recursive: true
                }
            }
        })
    </script>
</body>

</html>