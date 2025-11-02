<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" type="imagex/png" href="{{ url('assets/images/logos/intea/39.png') }}">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Intea - Cadastro</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth/create.css') }}">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"   />
</head>

<body>
    <!-- Voltar -->
    <div class="voltar">
        <a href="{{ route('register') }}">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
    </div>



    <!-- Logo -->
    <div class="logo-container">
        <a href="{{ route('register') }}">
            <img class="logo" src="{{ asset('assets/images/logos/intea/logo-lamp-cadastro.png') }}">
        </a>
    </div>

    @yield("main")

    <script>
        (function() {
            const form = document.getElementById('multiForm');
            const steps = Array.from(document.querySelectorAll('.step'));
            const phonesContainer = document.getElementById('phonesContainer');
            const addPhoneBtn = document.getElementById('addPhone');
            const photoInput = document.getElementById('foto');
            const photoPreview = document.getElementById('photoPreview');
            const maxPhones = 5;
            let current = 0;

            // Atualiza imagem
            photoInput.addEventListener('change', () => {
                const file = photoInput.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    photoPreview.innerHTML = `<img src="${e.target.result}" alt="Prévia"/>`;
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
                if (name === 'senha_confirmacao' && val !== form.querySelector('[name="senha"]').value) return 'Senhas diferentes';
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
                setTimeout(() => form.style.height = steps[i].offsetHeight + 'px', 60);
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

            addPhoneBtn.addEventListener('click', () => {
                const count = phonesContainer.querySelectorAll('.phone-row').length;
                if (count >= maxPhones) return;
                const row = document.createElement('div');
                row.className = 'phone-row';
                row.innerHTML = `<input name="numero_telefone[]" class="phone-input" type="tel" placeholder="(DD) 99999-9999" required /><button type="button" class="remove"><span class="material-symbols-outlined">check_indeterminate_small</span></button>`;
                phonesContainer.appendChild(row);
                row.querySelector('.remove').addEventListener('click', () => {
                    row.remove();
                    refreshButtons(current);
                    form.style.height = steps[current].offsetHeight + 'px';
                });
                row.querySelector('.phone-input').addEventListener('input', () => refreshButtons(current));
                form.style.height = steps[current].offsetHeight + 'px';
                refreshButtons(current);
            });

            form.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    const tag = document.activeElement.tagName;
                    if (tag === 'TEXTAREA') return;
                    e.preventDefault();
                    if (current === steps.length - 1) {
                        if (validateStep(current)) form.submit();
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