@extends('auth.template.layout')

@section('main')
<form id="multiForm" method="post" action="{{ route('comunidade.store') }}" enctype="multipart/form-data" novalidate>
  @csrf

  <!-- dados pessoais -->
  <div class="step active" data-step="0">
    <h2>Dados pessoais</h2>
    <label for="apelido">Nome *</label>
    <input id="apelido" name="apelido" type="text" maxlength="255" placeholder="Nome Usuário" required />
    <div class="error" data-error-for="apelido"></div>

    <label for="user">User *</label>
    <input id="user" class="user-input" name="user" type="text" maxlength="255" placeholder="@name" required />
    <div class="error" data-error-for="user"></div>

    <div class="controls">
      <div></div>
      <button type="button" class="btn primary next" disabled>Próximo</button>
    </div>
  </div>

  <!-- contato -->
  <div class="step" data-step="1">
    <h2>Contato</h2>
    <label for="email">Email *</label>
    <input id="email" name="email" type="email" required />
    <div class="error" data-error-for="email"></div>

    <label>Telefone(s) *</label>
    <div class="phones" id="phonesContainer">
      <div class="phone-row">
        <input name="numero_telefone[]" class="phone-input" type="tel" placeholder="(DD) 99999-9999" required />
      </div>
    </div>
    <button type="button" id="addPhone" class="add-phone">Adicionar telefone</button>
    <div class="error" data-error-for="numero_telefone"></div>

    <div class="controls">
      <button type="button" class="btn ghost prev">Anterior</button>
      <button type="button" class="btn primary next" disabled>Próximo</button>
    </div>
  </div>

  <!-- informações -->
  <div class="step" data-step="2">
    <h2>Informações</h2>
    <label for="data_nascimento">Data de Nascimento *</label>
    <input id="data_nascimento" name="data_nascimento" type="date" required />
    <div class="error" data-error-for="data_nascimento"></div>

    <label for="genero">Gênero *</label>
    <select id="genero" name="genero" required>
      <option value="">Selecione</option>
      @foreach ($generos as $genero)
      <option value="{{ $genero->id }}" {{ isset($usuario) && $item->id === $usuario->genero ? "selected='selected'": "" }}>{{ $genero->titulo }}</option>
      @endforeach
    </select>
    <div class="error" data-error-for="genero"></div>

    <div class="controls">
      <button type="button" class="btn ghost prev">Anterior</button>
      <button type="button" class="btn primary next" disabled>Próximo</button>
    </div>
  </div>

  <!-- senha -->
  <div class="step" data-step="3">
    <h2>Conta</h2>
    <label for="senha">Senha *</label>
    <input id="senha" name="senha" type="password" minlength="6" required />
    <div class="error" data-error-for="senha"></div>

    <label for="senha_confirmacao">Confirmar senha *</label>
    <input id="senha_confirmacao" name="senha_confirmacao" type="password" required />
    <div class="error" data-error-for="senha_confirmacao"></div>

    <input type="hidden" name="tipo_usuario" value="3" />
    <input type="hidden" name="status_conta" value="1" />

    <div class="controls">
      <button type="button" class="btn ghost prev">Anterior</button>
      <button type="button" class="btn primary next" disabled>Próximo</button>
    </div>
  </div>

  <!-- foto -->
  <div class="step" data-step="4">
    <h2>Foto de Perfil</h2>

    <div class="photo-preview" id="photoPreview">
      <span>Prévia</span>
    </div>

    <label for="foto">Selecione uma foto *</label>
    <input id="foto" name="foto" type="file" accept="image/png, image/jpeg, image/jpg, image/gif" required />
    <div class="error" data-error-for="foto"></div>

    <div class="controls">
      <button type="button" class="btn ghost prev">Anterior</button>
      <button type="submit" class="btn primary submit" disabled>Criar Conta</button>
    </div>
  </div>
</form>

@endsection