function abrirModalDenunciaUsuario(id) {
    document.getElementById('modal-denuncia-usuario-' + id).classList.remove('hidden');
}

function fecharModalDenunciaUsuario(id) {
    const modalDenunciaUsuario = document.getElementById('modal-denuncia-usuario-' + id);
    modalDenunciaUsuario.classList.add('hidden');

    const formDenunciaUsuario = modalDenunciaUsuario.querySelector("form");
    if (formDenunciaUsuario) formDenunciaUsuario.reset();
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia-usuario')) {
        event.target.classList.add('hidden');

        const formDenunciaUsuario = event.target.querySelector("form");
        if (formDenunciaUsuario) formDenunciaUsuario.reset();
    }
}