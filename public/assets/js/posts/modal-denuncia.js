function abrirModalDenuncia(id) {
    document.getElementById('modal-denuncia-postagem-' + id).classList.remove('hidden');
}

function fecharModalDenuncia(id) {
    const modalDenuncia = document.getElementById('modal-denuncia-postagem-' + id);
    modalDenuncia.classList.add('hidden');

    const formDenuncia = modalDenuncia.querySelector("form");
    if (formDenuncia) formDenuncia.reset();
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia-postagem')) {
        event.target.classList.add('hidden');

        const formDenuncia = event.target.querySelector("form");
        if (formDenuncia) formDenuncia.reset();
    }
}