function abrirModalEditar(id) {
    document.getElementById('modal-editar-' + id).classList.remove('hidden');
}
function fecharModalEditar(id) {
    const modalEditar = document.getElementById("modal-editar-" + id);
    modalEditar.classList.add("hidden");

    const formEditar = modalEditar.querySelector("form");
    if (formEditar) formEditar.reset();
}


const textarea = document.getElementById('post-textarea');
const charCount = document.getElementById('char-count');

textarea.addEventListener('input', () => {
    // auto-expand
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';

    // contador de caracteres
    const len = textarea.value.length;
    charCount.textContent = `${len}/255`;
});

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-editar')) {
        event.target.classList.add('hidden');

        const formEditar = event.target.querySelector("form");
        if (formEditar) formEditar.reset();
    }
}