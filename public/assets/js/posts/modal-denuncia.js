function abrirModal(id) {
    document.getElementById('modal-denuncia-' + id).classList.remove('hidden');
}

function fecharModal(id) {
    document.getElementById('modal-denuncia-' + id).classList.add('hidden');
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia')) {
        event.target.classList.add('hidden');
    }
}