function abrirModalEditar(id) {
    const modal = document.getElementById('modal-editar-postagem-' + id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'flex'; // garante visibilidade mesmo sem CSS global
        document.body.style.overflow = 'hidden'; // trava o scroll
    } else {
        console.warn('Modal de edição não encontrado para ID:', id);
    }
}

function fecharModalEditar(id) {
    const modal = document.getElementById('modal-editar-postagem-' + id);
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        document.body.style.overflow = ''; // libera scroll novamente
    }
}

// Fecha modal ao clicar fora do conteúdo
window.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        event.target.style.display = 'none';
        document.body.style.overflow = '';
    }
});
