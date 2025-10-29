function abrirModalEditarComentario(id) {
    const modalEditarComentario = document.getElementById('modal-editar-comentario-' + id);
    if (modalEditarComentario) {
        modalEditarComentario.classList.remove('hidden')
    };
}

function fecharModalEditar(id) {
    const modalEditarComentario = document.getElementById('modal-editar-comentario-' + id);
    if (modalEditarComentario) {
        modalEditarComentario.classList.add('hidden');
        const formEditarComentario = modalEditarComentario.querySelector('form');
        if (formEditarComentari) formEditarComentari.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const formEditarComentari = event.target.querySelector('form');
        if (formEditarComentari) formEditarComentari.reset();
    }
};

const textareaEditarComentario = document.getElementById('post-textarea_edit');
const charCountEditarComentario = document.getElementById('char-count');

textareaEditarComentario.addEventListener('input', () => {
    // auto-expand
    textareaEditarComentario.style.height = 'auto';
    textareaEditarComentario.style.height = textareaEditarComentario.scrollHeight + 'px';

    // contador de caracteres
    const lenEditarComentari = textareaEditarComentario.value.length;
    charCountEditarComentario.textContent = `${lenEditarComentari}/255`;
});