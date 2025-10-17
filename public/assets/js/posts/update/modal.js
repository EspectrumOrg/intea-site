function abrirModalEditar(id) {
    const modal = document.getElementById('modal-editar-' + id);
    if (modal) modal.classList.remove('hidden');
}

function fecharModalEditar(id) {
    const modal = document.getElementById('modal-editar-' + id);
    if (modal) {
        modal.classList.add('hidden');
        const form = modal.querySelector('form');
        if (form) form.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const form = event.target.querySelector('form');
        if (form) form.reset();
    }
};

const textarea = document.getElementById('post-textarea');
const charCount = document.getElementById('char-count');

textarea.addEventListener('input', () => {
    // auto-expand
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';

    // contador de caracteres
    const len = textarea.value.length;
    charCount.textContent = `${len}/255`;
});
