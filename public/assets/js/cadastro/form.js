function mostrarOutroGenero() {
    const select = document.getElementById('genero');
    const outroBox = document.getElementById('genero-outro-box');
    outroBox.style.display = (select.value === 'Outro') ? 'block' : 'none';
}

let contadorTelefone = 1;

function adicionarTelefone() {
if (contadorTelefone >= 5) return;

    contadorTelefone++;
    const container = document.getElementById('telefones');

    const novoCampo = document.createElement('div');
    novoCampo.classList.add('input-box-cadastro');
    novoCampo.innerHTML = `
        <label>Telefone ${contadorTelefone}</label>
        <input type="tel" name="telefone[]">
    `;

    container.appendChild(novoCampo);
}