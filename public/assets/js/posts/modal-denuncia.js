function abrirModalDenuncia(id) {
    document.getElementById('modal-denuncia-' + id).classList.remove('hidden');
}

function fecharModalDenuncia(id) {
    const modalDenuncia = document.getElementById('modal-denuncia-' + id);
    modalDenuncia.classList.add('hidden');

    const formDenuncia = modalDenuncia.querySelector("form");
    if (formDenuncia) formDenuncia.reset();
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia')) {
        event.target.classList.add('hidden');

        const formDenuncia = event.target.querySelector("form");
        if (formDenuncia) formDenuncia.reset();
    }
}