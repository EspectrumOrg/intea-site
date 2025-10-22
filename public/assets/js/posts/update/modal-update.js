function abrirModalEditar(id) {
    console.log("Abrindo modal", id); // <-- teste
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.remove('hidden')
    };
}

function fecharModalEditar(id) {
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.add('hidden');
        const form = modalEditarPostagem.querySelector('form');
        if (form) form.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const form = event.target.querySelector('form');
        if (form) form.reset();
    }
};

const textareaEditarPostagem = document.getElementById('post-textarea_edit');
const charCountEditarPostagem = document.getElementById('char-count');

textareaEditarPostagem.addEventListener('input', () => {
    // auto-expand
    textareaEditarPostagem.style.height = 'auto';
    textareaEditarPostagem.style.height = textareaEditarPostagem.scrollHeight + 'px';

    // contador de caracteres
    const len = textareaEditarPostagem.value.length;
    charCountEditarPostagem.textContent = `${len}/255`;
});