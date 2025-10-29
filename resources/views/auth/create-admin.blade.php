<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intea - Cadastro Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/create.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body>
    <!-- Voltar -->
    <div class="voltar">
        <a href="{{ route('dashboard.index') }}">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    </div>

    <!-- Logo -->
    <div class="logo-container">
        <a href="{{ route('dashboard.index') }}">
            <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp-cadastro.png') }}">
        </a>
    </div>

    <form id="multiFormAdmin" class="form-cadastro"  method="post" action="{{ route('admin.store') }}"> <!-- Formulário -->
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
            <div class="phones" id="phonesContainerAdmin">
                <div class="phone-row">
                    <input name="numero_telefone[]" class="phone-input" type="tel" placeholder="(DD) 99999-9999" required />
                </div>
            </div>
            <button type="button" id="addPhoneAdmin" class="add-phone">Adicionar telefone</button>
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

            <input type="hidden" name="tipo_usuario" value="1" />
            <input type="hidden" name="status_conta" value="1" />

            <div class="controls">
                <button type="button" class="btn ghost prev">Anterior</button>
                <button type="button" class="btn primary next" disabled>Próximo</button>
            </div>
        </div>

        <!-- foto -->
        <div class="step" data-step="4">
            <h2>Foto de Perfil</h2>

            <div class="photo-preview" id="photoPreviewAdmin">
                <span>Prévia</span>
            </div>

            <label for="foto">Selecione uma foto *</label>
            <input id="fotoAdmin" name="foto" type="file" accept="image/png, image/jpeg, image/jpg, image/gif" required />
            <div class="error" data-error-for="foto"></div>

            <div class="controls">
                <button type="button" class="btn ghost prev">Anterior</button>
                <button type="submit" class="btn primary submit" disabled>Criar Conta</button>
            </div>
        </div>
    </form>

    <script>
        (function() {
            const formAdmin = document.getElementById('multiFormAdmin');
            const steps = Array.from(document.querySelectorAll('.step'));
            const phonesContainerAdmin = document.getElementById('phonesContainerAdmin');
            const addPhoneBtnAdmin = document.getElementById('addPhoneAdmin');
            const photoInputAdmin = document.getElementById('fotoAdmin');
            const photoPreviewAdmin = document.getElementById('photoPreviewAdmin');
            const maxPhones = 5;
            let current = 0;

            // Atualiza imagem
            photoInputAdmin.addEventListener('change', () => {
                const file = photoInputAdmin.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    photoPreviewAdmin.innerHTML = `<img src="${e.target.result}" alt="Prévia"/>`;
                };
                reader.readAsDataURL(file);
                refreshButtons(current);
            });

            function setError(name, msg) {
                const el = document.querySelector(`.error[data-error-for="${name}"]`);
                if (el) el.textContent = msg || '';
            }

            function validateField(f) {
                const val = (f.value || '').trim();
                const name = f.name;
                if (f.required && val.length === 0) return 'Campo obrigatório';
                if (name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return 'Email inválido';
                if (name === 'senha' && val.length < 6) return 'Senha muito curta';
                if (name === 'senha_confirmacao' && val !== formAdmin.querySelector('[name="senha"]').value) return 'Senhas diferentes';
                if (name === 'numero_telefone[]' && val.replace(/\D/g, '').length < 8) return 'Telefone inválido';
                if (name === 'foto' && !f.files.length) return 'Envie uma imagem';
                return null;
            }

            function validateStep(i) {
                const stepEl = steps[i];
                const fields = Array.from(stepEl.querySelectorAll('[required]'));
                let ok = true;
                fields.forEach(f => {
                    const err = validateField(f);
                    setError(f.name === 'numero_telefone[]' ? 'numero_telefone' : f.name, err);
                    if (err) ok = false;
                });
                return ok;
            }

            function refreshButtons(i) {
                const step = steps[i];
                const next = step.querySelector('.next');
                const submit = step.querySelector('.submit');
                const valid = validateStep(i);
                if (next) next.disabled = !valid;
                if (submit) submit.disabled = !valid;
            }

            function showStep(i) {
                current = i;
                steps.forEach((s, idx) => s.classList.toggle('active', idx === i));
                setTimeout(() => formAdmin.style.height = steps[i].offsetHeight + 'px', 60);
                refreshButtons(i);
            }

            document.querySelectorAll('.next').forEach(b => b.addEventListener('click', () => {
                if (validateStep(current)) showStep(current + 1);
            }));
            document.querySelectorAll('.prev').forEach(b => b.addEventListener('click', () => showStep(current - 1)));

            steps.forEach((s, i) =>
                s.querySelectorAll('input,select').forEach(inp => {
                    inp.addEventListener('input', () => refreshButtons(i));
                    inp.addEventListener('blur', () => refreshButtons(i));
                })
            );

            addPhoneBtnAdmin.addEventListener('click', () => {
                const count = phonesContainerAdmin.querySelectorAll('.phone-row').length;
                if (count >= maxPhones) return;
                const row = document.createElement('div');
                row.className = 'phone-row';
                row.innerHTML = `<input name="numero_telefone[]" class="phone-input" type="tel" placeholder="(DD) 99999-9999" required /><button type="button" class="remove"><span class="material-symbols-outlined">check_indeterminate_small</span></button>`;
                phonesContainerAdmin.appendChild(row);
                row.querySelector('.remove').addEventListener('click', () => {
                    row.remove();
                    refreshButtons(current);
                    formAdmin.style.height = steps[current].offsetHeight + 'px';
                });
                row.querySelector('.phone-input').addEventListener('input', () => refreshButtons(current));
                formAdmin.style.height = steps[current].offsetHeight + 'px';
                refreshButtons(current);
            });

            formAdmin.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    const tag = document.activeElement.tagName;
                    if (tag === 'TEXTAREA') return;
                    e.preventDefault();
                    if (current === steps.length - 1) {
                        if (validateStep(current)) formAdmin.submit();
                    } else if (validateStep(current)) showStep(current + 1);
                }
            });

            window.addEventListener('load', () => showStep(0));
        })();
    </script>

    <!-- JQuery-->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Máscara-->
    <script>
        $('.phone-input').mask('(00) 00000-0000')
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

</html>