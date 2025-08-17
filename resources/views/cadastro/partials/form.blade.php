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
    <div class="telefone" id="telefones">
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
    </div>

    <div class="field">
        <label>Data de Nascimento *</label>
        <input type="date" name="data_nascimento" value="{{ $usuario->data_nascimento ?? old('data_nascimento') }}">
    </div>

    <div class="field">
        <label>Gênero</label>
        <select type="text" id="genero" name="genero">
            <option value="">Opções</option>
            @foreach($generos as $item)
            <option value="{{ $item->id }}" {{ isset($usuario) && $item->id === $usuario->genero ? "selected='selected'": "" }}>{{ $item->titulo }}</option>
            @endforeach
        </select>
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
        <input type="password" name="senha">
    </div>

    <div class="field">
        <label>Confirmar Senha *</label>
        <input type="password" name="senha_confirmacao">
    </div>

    <div class="field btns"> <!-- btns -->
        <button type="button" class="prev-3 prev">Anterior</button>
        <button type="submit" class="botao-registro submit">Criar Conta</button>
    </div>
</div>