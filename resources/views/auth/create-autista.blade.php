@extends('auth.template.layout')

@section('main')
<link rel="stylesheet" href="{{ asset('assets/css/auth/create-autista.css') }}">

<form id="multiForm" method="post" action="{{ route('autista.store') }}" enctype="multipart/form-data" novalidate>
    @csrf

    <!-- DADOS PESSOAIS -->
    <div class="step active" data-step="0">
        <h2>Dados Pessoais</h2>
        <label for="nome">Nome Completo *</label>
        <input id="nome" name="nome" type="text" placeholder="Nome Sobrenome" required />
        <div class="error" data-error-for="nome"></div>

        <label for="apelido">Nome de Usuário *</label>
        <input id="apelido" name="apelido" type="text" placeholder="nomeConta" required />
        <div class="error" data-error-for="apelido"></div>

        <div class="controls">
            <div></div>
            <button type="button" class="btn primary next" disabled>Próximo</button>
        </div>
    </div>

    <!-- IDENTIFICAÇÃO -->
    <div class="step" data-step="1">
        <h2>Identificação</h2>

        <label for="cpf">CPF *</label>
        <input id="cpf" name="cpf" type="text" maxlength="14" placeholder="123.456.789-10" required />
        <div class="error" data-error-for="cpf"></div>

        <label for="CipteiaAutista">CIPTEA *</label>
        <input id="CipteiaAutista" name="CipteiaAutista" type="text" required />
        <div class="error" data-error-for="CipteiaAutista"></div>

        <label for="data_nascimento">Data de Nascimento *</label>
        <input id="data_nascimento" name="data_nascimento" type="date" onchange="verificarIdade()" required />
        <div class="error" data-error-for="data_nascimento"></div>

        <div id="cpf_responsavel_field" style="display:none;">
            <label for="cpf_responsavel">CPF do Responsável *</label>
            <input id="cpf_responsavel" name="cpf_responsavel" type="text" maxlength="14" placeholder="123.456.789-00" />
            <div class="error" data-error-for="cpf_responsavel"></div>
        </div>

        <label for="genero">Gênero *</label>
        <select id="genero" name="genero" required>
            <option value="">Selecione</option>
            @foreach ($generos as $item)
            <option value="{{ $item->id }}">{{ $item->titulo }}</option>
            @endforeach
        </select>
        <div class="error" data-error-for="genero"></div>

        <div class="controls">
            <button type="button" class="btn ghost prev">Anterior</button>
            <button type="button" class="btn primary next" disabled>Próximo</button>
        </div>
    </div>

    <!-- CONTATO -->
    <div class="step" data-step="2">
        <h2>Contato</h2>
        <label for="email">Email *</label>
        <input id="email" name="email" type="email" placeholder="name@example.com" required />
        <div class="error" data-error-for="email"></div>

        <label>Telefone(s) *</label>
        <div id="phonesContainer" class="phones">
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

    <!-- CONTA -->
    <div class="step" data-step="3">
        <h2>Conta</h2>
        <label for="user">User *</label>
        <input id="user" name="user" type="text" placeholder="@usuario" required />
        <div class="error" data-error-for="user"></div>

        <label for="senha">Senha *</label>
        <input id="senha" name="senha" type="password" minlength="6" required />
        <div class="error" data-error-for="senha"></div>

        <label for="senha_confirmacao">Confirmar Senha *</label>
        <input id="senha_confirmacao" name="senha_confirmacao" type="password" required />
        <div class="error" data-error-for="senha_confirmacao"></div>

        <input type="hidden" name="tipo_usuario" value="2" />
        <input type="hidden" name="status_conta" value="1" />

        <div class="controls">
            <button type="button" class="btn ghost prev">Anterior</button>
            <button type="button" class="btn primary next" disabled>Próximo</button>
        </div>
    </div>

    <!-- FOTO -->
    <div class="step" data-step="4">
        <h2>Foto de Perfil</h2>
        <div class="photo-preview" id="photoPreview"><span>Prévia</span></div>

        <label for="foto">Selecione uma foto *</label>
        <input id="foto" name="foto" type="file" accept="image/*" required />
        <div class="error" data-error-for="foto"></div>

        <div class="controls">
            <button type="button" class="btn ghost prev">Anterior</button>
            <button type="submit" class="btn primary submit" disabled>Criar Conta</button>
        </div>
    </div>
</form>


<script>
    function verificarIdade() {
        const inputData = document.getElementById('data_nascimento');
        const cpfResponsavelField = document.getElementById('cpf_responsavel_field');
        const dataValor = inputData.value;

        if (!dataValor) {
            cpfResponsavelField.style.display = 'none';
            return;
        }

        const hoje = new Date();
        const nascimento = new Date(dataValor);
        let idade = hoje.getFullYear() - nascimento.getFullYear();
        const mes = hoje.getMonth() - nascimento.getMonth();

        if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
            idade--;
        }

        if (idade < 18) {
            cpfResponsavelField.style.display = 'block';
        } else {
            cpfResponsavelField.style.display = 'none';
        }
    }

    // Verifica também ao carregar a página (útil após erro de validação)
    window.addEventListener('DOMContentLoaded', verificarIdade);
</script>
@endsection