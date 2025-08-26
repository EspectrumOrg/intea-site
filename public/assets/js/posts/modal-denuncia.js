function abrirModal(id) {
    document.getElementById('modal-denuncia-' + id).style.display = 'flex';
}

function fecharModal(id) {
    document.getElementById('modal-denuncia-' + id).style.display = 'none';
}

// fecha clicando fora do modal
window.onclick = function(event) {
    if (event.target.classList.contains('modal-denuncia')) {
        event.target.style.display = 'none';
    }
}