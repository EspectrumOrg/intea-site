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
            <h1>Cadastro - Responsável</h1>
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
            <form class="form-cadastro" method="post" action="{{ route('cadastro.store.responsavel') }}">
                @csrf
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <input type="hidden" name="tipo_usuario" value="5">
                <input type="hidden" name="status_conta" value="1">

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

                    <div class="field">
                        <label>Cipteia (não sei como vai funcionar na prática)</label>
                        <input type="text" name="cipteia_autista" value="{{ $usuario->cipteia_autista ?? old('cipteia_autista') }}">
                    </div>

                    <div class="field nextBtn">
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

                    <div class="field btns">
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
                        <label>Cipteia do seu protegido *</label>
                        <input type="text" name="cipteia" required>
                    </div>

                    <div class="field">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="data_nascimento" value="{{ $usuario->data_nascimento ?? old('data_nascimento') }}">
                    </div>

                    <div class="field">
                        <label>Gênero *</label>
                        <select name="genero" id="genero" onchange="mostrarOutroGenero()">
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

                    <div class="field btns">
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
                        <input type="password" name="senha" placeholder="minimo 6 caracteres" required>
                    </div>

                    <div class="field">
                        <label>Confirmar Senha *</label>
                        <input type="password" name="senha_confirmation" required>
                    </div>
                    <div class="field btns"> <!-- btns cipteia_autista -->
                        <button type="button" class="prev-3 prev">Anterior</button>
                        <button type="submit" class="botao-registro submit">Criar Conta</button>
                    </div>
                </div>
            </form>
            <div class="voltar">
                <p><a href="{{ route('cadastro.index') }}">Voltar para tipo conta</a></p>
            </div>
        </div>
    </main>

    <script src="{{ asset('assets/js/cadastro/form.js') }}"></script>
    <script src="{{ asset('assets/js/cadastro/progresso.js') }}"></script>
</body>

</html>