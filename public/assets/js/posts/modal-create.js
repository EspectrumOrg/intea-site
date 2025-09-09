function abrirModalPostar() {
    document.getElementById('modal-postar').classList.remove('hidden');
}
function fecharModalPostar() {
    const modalPostar = document.getElementById('modal-postar');
    modalPostar.classList.add('hidden');

    const form = modalPostar.querySelector("form");
    if (form) form.reset();
}


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