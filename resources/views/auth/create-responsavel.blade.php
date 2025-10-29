<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - cadastro</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/cadastro.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body>

    <!-- parte form-outer e form -->
    <main class="container-cadastro">
        <div class="form-outer">
            <h2>Cadastro - Responsável</h2>
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

            @if ($errors->any())
            <div class="alert-error">
                <ul>
                    <li>erro na validação de dados</li>
                </ul>
            </div>
            @endif

            <form class="form-cadastro" method="post" action="{{ route('responsavel.store') }}">
                @csrf
                <input type="hidden" name="tipo_usuario" value="5">
                <input type="hidden" name="status_conta" value="1">

                <div class="page slidepage"> <!-- Início -->
                    <div class="title">Seu Nome:</div>
                    <div class="field">
                        <label>Nome Completo *</label>
                        <input
                            type="text"
                            name="nome"
                            value="{{ $usuario->nome ?? old('nome') }}"
                            placeholder="Nome Sobrenome">

                        @if ($errors->has('nome'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('nome') }}</h3>
                        </div>
                        @endif
                    </div>

                    <div class="field">
                        <label>Nome Conta</label>
                        <input
                            type="text"
                            name="apelido"
                            value="{{ $usuario->apelido ?? old('apelido') }}"
                            placeholder="nomeConta">
                    </div>

                    <div class="field">
                        <label>Cipteia</label>
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
                        <input
                            type="email"
                            name="email"
                            value="{{ $usuario->email ?? old('email') }}"
                            placeholder="name@example.com">

                        @if ($errors->has('email'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('email') }}</h3>
                        </div>
                        @endif
                    </div>

                    <!-- Telefone -->
                    <div class="telefone" id="telefones">
                        @php
                        $telefones = old('numero_telefone', ['']);
                        @endphp

                        <label>Telefone(s) - até 5</label>
                        @foreach ($telefones as $index => $tel)
                        <div class="input-box-cadastro">
                            <input
                                type="tel"
                                class="telefone-input"
                                name="numero_telefone[]"
                                value="{{ $tel }}"
                                placeholder="(DD) 12345-6789">
                        </div>
                        @endforeach

                        @foreach ($errors->get('numero_telefone.*') as $mensagens)
                        @foreach ($mensagens as $mensagem)
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $mensagem }}</h3>
                        </div>
                        @endforeach
                        @endforeach
                    </div>

                    <div class="btn-telefone">
                        <button type="button" class="botao-telefone" onclick="adicionarTelefone()">Adicionar Telefone</button>
                    </div>

                    <div class="field btns">
                        <button type="button" class="prev-1 prev">Anterior</button>
                        <button type="button" class="next-1 next">Próximo</button>
                    </div>
                </div>

                <div class="page slidepage"> <!-- Informações -->
                    <div class="title">Informações:</div>
                    <div class="field">
                        <label>CPF *</label>
                        <input
                            type="text"
                            name="cpf"
                            value="{{ $usuario->cpf ?? old('cpf') }}"
                            class="cpf-input"
                            placeholder="123.456.789-10">

                        @if ($errors->has('cpf'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('cpf') }}</h3>
                        </div>
                        @endif
                    </div>

                    <div class="field">
                        <label>Data de Nascimento *</label>
                        <input type="date" name="data_nascimento" value="{{ $usuario->data_nascimento ?? old('data_nascimento') }}">

                        @if ($errors->has('data_nascimento'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('data_nascimento') }}</h3>
                        </div>
                        @endif
                    </div>

                    <div class="field">
                        <label>Gênero</label>
                        <select type="text" id="genero" name="genero">
                            <option value="">--- Selecione ---</option>
                            @foreach($generos as $item)
                            <option value="{{ $item->id }}" {{ isset($usuario) && $item->id === $usuario->genero ? "selected='selected'": "" }}>{{ $item->titulo }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('genero'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('genero') }}</h3>
                        </div>
                        @endif
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
                        <input
                            type="text"
                            name="user"
                            value="{{ $usuario->user ?? old('user') }}"
                            class="user-input"
                            placeholder="@exemploNome">

                        @if ($errors->has('user'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('user') }}</h3>
                        </div>
                        @endif
                    </div>
                    <div class="field">
                        <label>Senha *</label>
                        <input type="password" name="senha">

                        @if ($errors->has('senha'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('senha') }}</h3>
                        </div>
                        @endif
                    </div>

                    <div class="field">
                        <label>Confirmar Senha *</label>
                        <input type="password" name="senha_confirmacao">

                        @if ($errors->has('senha_confirmacao'))
                        <div class="alert alert-danger">
                            <h3 class="alert-mensage">{{ $errors->first('senha_confirmacao') }}</h3>
                        </div>
                        @endif
                    </div>
                    <div class="field btns"> <!-- btns cipteia_autista -->
                        <button type="button" class="prev-3 prev">Anterior</button>
                        <button type="submit" class="botao-registro submit">Criar Conta</button>
                    </div>
                </div>
            </form>
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