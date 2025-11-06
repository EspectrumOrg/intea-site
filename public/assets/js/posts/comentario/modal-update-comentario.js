function abrirModalEditarComentario(id) {
    const modalEditarComentario = document.getElementById('modal-editar-comentario-' + id);
    if (modalEditarComentario) {
        modalEditarComentario.classList.remove('hidden')
    };
}

function fecharModalEditarComentario(id) {
    const modalEditarComentario = document.getElementById('modal-editar-comentario-' + id);
    if (modalEditarComentario) {
        modalEditarComentario.classList.add('hidden');
        const formEditarComentario = modalEditarComentario.querySelector('form');
        if (formEditarComentario) formEditarComentario.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const formEditarComentario = event.target.querySelector('form');
        if (formEditarComentario) formEditarComentario.reset();
    }
};

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".textarea-comentario-edit").forEach(textarea => {
        const idComentarioEditModal = textarea.dataset.id;
        const counterComentarioEditModal = document.querySelector(`.char-count-comentario-edit[data-id="${idComentarioEditModal}"]`);

        textarea.addEventListener("input", () => {

        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";

        counterComentarioEditModal.textContent = `${textarea.value.length}/255`;
        counterComentarioEditModal.style.color = textarea.value.length >= 255 ? "red" : "";

        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";
        counterComentarioEditModal.textContent = `${textarea.value.length}/280`;
        });
    })
})