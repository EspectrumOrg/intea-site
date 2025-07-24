<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - Comunidade</title>
    <link rel="stylesheet" href="{{ asset('assets/css/cadastro/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="header">
        <img src="{{ asset('assets/images/logos/intea/logo.png') }}" alt="Logo" class="logo">
    </header>

    <main class="container-cadastro">
        <div class="form-outer">
            <h1>Cadastro - Participante da Comunidade</h1>
            <p>Preencha todos os campos obrigatórios (*)</p>

            <div class="progress-bar">
                <div class="step">
                    <p>Nome</p>
                    <div class="bullet">
                        <span>1</span>
                    </div>
                    <div class="check fas fa-check"></div>
                </div>

                <div class="step">
                    <p>Contato</p>
                    <div class="bullet">
                        <span>2</span>
                    </div>
                    <div class="check fas fa-check"></div>
                </div>

                <div class="step">
                    <p>Informações</p>
                    <div class="bullet">
                        <span>3</span>
                    </div>
                    <div class="check fas fa-check"></div>
                </div>

                <div class="step">
                    <p>Conta</p>
                    <div class="bullet">
                        <span>4</span>
                    </div>
                    <div class="check fas fa-check"></div>
                </div>
            </div>
            <!-- No topo do formulário -->
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $erro)
                    <li><h3 class="alert-mensage">{{ $erro }}</h3></li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form class="form-cadastro" method="post" action="{{ route('cadastro.store.comunidade') }}"> <!-- Formulário -->
                @csrf
                <input type="hidden" name="tipo_usuario" value="3"> <!-- Tipo User Comunidade-->
                <input type="hidden" name="status_conta" value="1"> <!-- 1 = ativo, 0 = inativo-->

                @include("cadastro.partials.form")
            </form>
            <div class="voltar">
                <p><a href="{{ route('cadastro.index') }}">Tipo conta</a></p>
                <p><a href="{{ route('welcome') }}">Início</a></p>
            </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/cadastro/form.js') }}"></script>
    <script src="{{ asset('assets/js/cadastro/progresso.js') }}"></script>
</body>

</html>