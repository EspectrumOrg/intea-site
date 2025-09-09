@extends('auth.template.layout')

@section('main')
<div class="form-outer">
    <div class="descricao">
        <h2>Cadastro - Comunidade</h2>
        <p>Preencha todos os campos obrigatórios (*)</p>
    </div>


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

    <form class="form-cadastro" method="post" action="{{ route('comunidade.store') }}"> <!-- Formulário -->
        @csrf
        <input type="hidden" name="tipo_usuario" value="3"> <!-- Tipo User Comunidade-->
        <input type="hidden" name="status_conta" value="1"> <!-- 1 = ativo, 0 = inativo-->

        <div class="page slidepage"> <!-- Início -->
            <div class="title">Seu Nome:</div>
            <div class="field">
                <label>Nome Completo *</label>
                <input type="text" name="nome" value="{{ $usuario->nome ?? old('nome') }}">

                @if ($errors->has('nome'))
                <div class="alert alert-danger">
                    <h3 class="alert-mensage">{{ $errors->first('nome') }}</h3>
                </div>
                @endif
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
                    <input type="tel" name="numero_telefone[]" value="{{ $tel }}">
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
                    <option value="">Opções</option>
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

            <div class="field btns"> <!-- btns -->
                <button type="button" class="prev-3 prev">Anterior</button>
                <button type="submit" class="botao-registro submit">Criar Conta</button>
            </div>
        </div>
    </form>
    @endsection