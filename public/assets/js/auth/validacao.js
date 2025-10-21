document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.form-cadastro');
    if (!form) return;

    // === Funções auxiliares ===
    function showError(input, message) {
        removeError(input);
        const errorDiv = document.createElement('div');
        errorDiv.classList.add('alert', 'alert-danger');
        errorDiv.innerHTML = `<h3 class="alert-mensage">${message}</h3>`;
        input.insertAdjacentElement('afterend', errorDiv);
    }

    function removeError(input) {
        const next = input.nextElementSibling;
        if (next && next.classList.contains('alert-danger')) {
            next.remove();
        }
    }

    // === Regras de validação ===
    const rules = {
        nome: value => value.trim().length >= 5 || 'Insira um nome (mínimo 5 letras)',
        apelido: value => value.trim().length >= 3 || 'Insira um apelido (mínimo 3 letras)',
        user: value => value.trim().length >= 4 || 'Insira um user (mínimo 4 caracteres)',
        email: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || 'Insira um e-mail válido',
        senha: value => value.length >= 6 || 'A senha deve ter pelo menos 6 caracteres',
        senha_confirmacao: (value, form) => value === form.querySelector('[name="senha"]').value || 'As senhas não coincidem',
        cpf: value => /^\d{3}\.?\d{3}\.?\d{3}\-?\d{2}$/.test(value) || 'CPF inválido',
        data_nascimento: value => !!value || 'Informe sua data de nascimento',
        genero: value => value.trim() !== '' || 'Selecione um gênero'
    };

    // === Validação dinâmica ===
    function validateField(input) {
        const rule = rules[input.name];
        if (!rule) return;

        const result = rule(input.value, form);
        if (result !== true) {
            showError(input, result);
        } else {
            removeError(input);
        }
    }

    // === Valida automaticamente ao carregar (mensagens visíveis sem digitar) ===
    Object.keys(rules).forEach(name => {
        const input = form.querySelector(`[name="${name}"]`);
        if (input) validateField(input);
    });

    // === Eventos de digitação e saída de campo ===
    form.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('blur', () => validateField(input));
    });

    // === Validação para telefones ===
    function validateTelefones() {
        const telefoneInputs = form.querySelectorAll('.telefone-input');
        telefoneInputs.forEach(input => {
            const value = input.value.trim();
            const next = input.nextElementSibling;
            if (next && next.classList.contains('alert-danger')) next.remove();

            const valido = /^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/.test(value);
            if (!valido) {
                const errorDiv = document.createElement('div');
                errorDiv.classList.add('alert', 'alert-danger');
                errorDiv.innerHTML = '<h3 class="alert-mensage">Insira um telefone</h3>';
                input.insertAdjacentElement('afterend', errorDiv);
            }
        });
    }

    // Valida telefones existentes ao carregar
    validateTelefones();

    // Revalida sempre que o usuário digitar ou adicionar novo telefone
    form.addEventListener('input', e => {
        if (e.target.classList.contains('telefone-input')) validateTelefones();
    });
});
