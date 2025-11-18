@extends('auth.template.layout')

@section('main')

<div class="page-create-autista">

    <link rel="stylesheet" href="{{ asset('assets/css/auth/create-autista.css') }}">

    <form id="multiForm" method="post" action="{{ route('autista.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- DADOS PESSOAIS -->
        <div class="step active" data-step="0">
            <br>
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
            <br>
        </div>

        <!-- IDENTIFICAÇÃO -->
        <div class="step" data-step="1">
            <br>
            <h2>Identificação</h2>

            <label for="cpf">CPF *</label>
            <input id="cpf" name="cpf" type="text" maxlength="14" placeholder="123.456.789-10" class="cpf-input" required />
            <div class="error" data-error-for="cpf"></div>

            <label for="CipteiaAutista">CIPTEA *</label>
            <input id="CipteiaAutista" name="CipteiaAutista" type="text" required />
            <div class="error" data-error-for="CipteiaAutista"></div>

            <label for="data_nascimento">Data de Nascimento *</label>
            <input id="data_nascimento" name="data_nascimento" type="date" onchange="verificarIdade()" required />
            <div class="error" data-error-for="data_nascimento"></div>

            <div id="cpf_responsavel_field" style="display:none;">
                <label for="cpf_responsavel">CPF do Responsável *</label>
                <input id="cpf_responsavel" name="cpf_responsavel" class="cpf-input" type="text" maxlength="14" placeholder="123.456.789-00" />
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
            <br>
        </div>

        <!-- CONTATO -->
        <div class="step" data-step="2">
            <br>
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
            <br>
        </div>

        <!-- CONTA -->
        <div class="step" data-step="3">
            <br>
            <h2>Conta</h2>
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
            <br>
        </div>

        <!-- FOTO -->
        <div class="step" data-step="4">
            <br>
            <h2>Foto de Perfil</h2>
            <div class="photo-preview" id="photoPreview"><span>Prévia</span></div>

            <label for="foto">Selecione uma foto *</label>
            <div class="extras">
                <label for="foto" class="upload-label">
                    <span class="material-symbols-outlined">image</span>
                </label>
                <input id="foto" name="foto" type="file" accept="image/png, image/jpeg, image/jpg, image/gif" class="input-file" required>
                <x-input-error class="mt-2" :messages="$errors->get('foto')" />
            </div>

            <div class="controls">
                <button type="button" class="btn ghost prev">Anterior</button>
                <button type="submit" class="btn primary submit" disabled>Criar Conta</button>
            </div>
            <br>
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

            cpfResponsavelField.style.display = idade < 18 ? 'block' : 'none';
        }

        window.addEventListener('DOMContentLoaded', verificarIdade);

        window.addEventListener('DOMContentLoaded', () => {
            const steps = document.querySelectorAll('.step');
            const form = document.querySelector('form');

            let maxHeight = 0;

            steps.forEach(step => {
                step.style.display = 'block'; // força medir
                const h = step.offsetHeight;
                if (h > maxHeight) maxHeight = h;
                step.style.display = ''; // volta ao normal
            });

            form.style.height = maxHeight + 100 + 'px'; // +100 para margem interna
            form.style.overflowY = 'auto';
        });
    </script>
</div> {{-- FECHA page-create-autista --}}
@endsection