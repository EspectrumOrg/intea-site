function abrirModalEditar(id) {
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.remove('hidden')
    };
}

function fecharModalEditar(id) {
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.add('hidden');
        const formEditarPostagem = modalEditarPostagem.querySelector('form');
        if (formEditarPostagem) formEditarPostagem.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const formEditarPostagem = event.target.querySelector('form');
        if (formEditarPostagem) formEditarPostagem.reset();
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