function mostrarOutroGenero() {
    const select = document.getElementById('genero');
    const outroBox = document.getElementById('genero-outro-box');
    outroBox.style.display = (select.value === 'Outro') ? 'block' : 'none';
}

let contadorTelefone = document.querySelectorAll('#telefones input[type="tel"]').length;

function adicionarTelefone() {
    if (contadorTelefone >= 5) return;

    contadorTelefone++;
    const container = document.getElementById('telefones');

    const novoCampo = document.createElement('div');
    novoCampo.classList.add('input-box-cadastro');
    novoCampo.innerHTML = `
        <input type="tel" class="telefone-input" name="numero_telefone[]" placeholder="(DD) 12345-6789">
    `;

    container.appendChild(novoCampo);

    // aplica a máscara só no novo input
    $(novoCampo).find('.telefone-input').mask('(00) 00000-0000')
}
