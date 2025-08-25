function toggleForm(postId) {
    const form = document.getElementById(`form-comentario-${postId}`);
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
}

function carregarMais(postId) {
    const comentarios = document.querySelectorAll(`#comentarios-${postId} .hidden`);
    comentarios.forEach(c => c.classList.remove('hidden'));
    document.querySelector(`#comentarios-${postId} .carregar-mais`)?.remove();
}
