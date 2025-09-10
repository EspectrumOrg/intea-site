function abrirModalImagem(element) {
    const modal = document.getElementById('modalPerfil');
    const modalImg = document.getElementById('modalImg');
    modalImg.src = element.src;
    modal.classList.remove('hidden');
}

function fecharModalImagem() {
    document.getElementById('modalPerfil').classList.add('hidden');
}

// Fecha ao clicar fora da imagem
document.getElementById('modalPerfil').addEventListener('click', function(e) {
    if (e.target.id === 'modalPerfil') {
        fecharModaldImagem();
    }
});