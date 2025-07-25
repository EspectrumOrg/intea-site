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
            <h1>Cadastro - Autista</h1>
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
                    <li>
                        <h3 class="alert-mensage">{{ $erro }}</h3>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="form-cadastro" method="post" action="{{ route('cadastro.store.autista') }}"> <!-- Formulário -->
                @csrf
                <input type="hidden" name="tipo_usuario" value="2"> <!-- Tipo User Autista-->
                <input type="hidden" name="status_conta" value="1"> <!-- 1 = ativo, 0 = inativo-->

                <div class="page slidepage"> <!-- Início -->
                    <div class="title">Seu Nome:</div>
                    <div class="field">
                        <label>Nome Completo *</label>
                        <input type="text" name="nome" value="{{ $usuario->nome ?? old('nome') }}">
                    </div>

                    <div class="field">
                        <label>Como quer ser chamado no site</label>
                        <input type="text" name="apelido" value="{{ $usuario->apelido ?? old('apelido') }}">
                    </div>

                    <div class="field nextBtn"> <!-- btns -->
                        <button type="button" class="next">Próximo</button>
                    </div>
                </div>

                <div class="page slidepage"> <!-- Contato -->
                    <div class="title">Contato:</div>
                    <div class="field">
                        <label>Email *</label>
                        <input type="email" name="email" value="{{ $usuario->email ?? old('email') }}">
                    </div>

                    <!-- Telefone -->
                    <div id="telefones">
                        @php
                        $telefones = old('numero_telefone', ['']);
                        @endphp

                        @foreach ($telefones as $index => $tel)
                        <div class="input-box-cadastro">
                            <label>Telefone {{ $index + 1 }}</label>
                            <input type="tel" name="numero_telefone[]" value="{{ $tel }}">
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="botao-telefone" onclick="adicionarTelefone()">Adicionar Telefone</button>

                    <div class="field btns"> <!-- btns -->
                        <button type="button" class="prev-1 prev">Anterior</button>
                        <button type="button" class="next-1 next">Próximo</button>
                    </div>
                </div>

                <div class="page slidepage"> <!-- Informações -->
                    <div class="title">Informações:</div>
                    <div class="field">
                        <label>CPF *</label>
                        <input type="text" name="cpf" value="{{ $usuario->cpf ?? old('cpf') }}">
                    </div>

                    <div class="field">
                        <label>RG *</label>
                        <input type="text" name="rg_autista" required>
                    </div>

                    <div class="field">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="data_nascimento" value="{{ $usuario->data_nascimento ?? old('data_nascimento') }}">
                    </div>

                    <div class="field">
                        <label>Gênero *</label>
                        <select name="genero" id="genero" onchange="mostrarOutroGenero()" required>
                            <option value="">Selecione</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Não Binario">Não Binário</option>
                            <option value="Prefiro não informar">Prefiro não informar</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="field" id="genero-outro-box" style="display: none;">
                        <label>Informe o gênero:</label>
                        <input type="text" name="genero_outro">
                    </div>

                    <div class="field btns"> <!-- btns -->
                        <button type="button" class="prev-2 prev">Anterior</button>
                        <button type="button" class="next-2 next">Próximo</button>
                    </div>
                </div>

                <div class="page slidepage"> <!-- Login -->
                    <div class="title">Conta:</div>
                    <div class="field">
                        <label>Seu USER *</label>
                        <input type="text" name="user" value="{{ $usuario->user ?? old('user') }}">
                    </div>
                    <div class="field">
                        <label>Senha *</label>
                        <input type="password" name="senha" required>
                    </div>

                    <div class="field">
                        <label>Confirmar Senha *</label>
                        <input type="password" name="senha_confirmacao" required>
                    </div>

                    <div class="field btns"> <!-- btns -->
                        <button type="button" class="prev-3 prev">Anterior</button>
                        <button type="submit" class="botao-registro submit">Criar Conta</button>
                    </div>
                </div>
            </form>
            <div class="voltar">
                <p><a href="{{ route('register') }}">Voltar para tipo conta</a></p>
            </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/cadastro/form.js') }}"></script>
    <script src="{{ asset('assets/js/cadastro/progresso.js') }}"></script>
</body>

</html>