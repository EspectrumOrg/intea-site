function abrirModalDenunciaComentario(id) {
    document.getElementById('modal-denuncia-comentario-' + id).classList.remove('hidden');
}

function fecharModalDenunciaComentario(id) {
    const modalDenunciaComentario = document.getElementById('modal-denuncia-comentario-' + id);
    modalDenunciaComentario.classList.add('hidden');

    const formDenunciaComentario = modalDenunciaComentario.querySelector("form");
    if (formDenunciaComentario) formDenunciaComentario.reset();
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia-comentario')) {
        event.target.classList.add('hidden');

        const formDenunciaComentario = event.target.querySelector("form");
        if (formDenunciaComentario) formDenunciaComentario.reset();
    }
}