function abrirModalEditar(id) {
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.remove('hidden')
    };
}

function fecharModalEditarPostagem(id) {
    const modalEditarPostagem = document.getElementById('modal-editar-postagem-' + id);
    if (modalEditarPostagem) {
        modalEditarPostagem.classList.add('hidden');
        const formEditarPostagem = modalEditarPostagem.querySelector('form');
        if (formEditarPostagem) formEditarPostagem.reset();
    }
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.add('hidden');
        const formEditarPostagem = event.target.querySelector('form');
        if (formEditarPostagem) formEditarPostagem.reset();
    }
}  

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".textarea-postagem-edit").forEach(textarea => {
        const id = textarea.dataset.id;
        const counter = document.querySelector(`.char-count-postagem-edit[data-id="${id}"]`);

        textarea.addEventListener("input", () => {

        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";

        counter.textContent = `${textarea.value.length}/255`;
        counter.style.color = textarea.value.length >= 255 ? "red" : "";

        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";
        counter.textContent = `${textarea.value.length}/280`;
        });
    })
})