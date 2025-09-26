document.addEventListener("DOMContentLoaded", function () {
    const textareaComentario = document.getElementById("texto_comentario");
    const counterComentario = document.getElementById("char-count-comentario");
    const maxComentario = textareaComentario.getAttribute("maxlength");

    // Expande conforme digita
    textareaComentario.addEventListener("input", () => {
        textareaComentario.style.height = "auto"; // reseta antes de recalcular
        textareaComentario.style.height = textareaComentario.scrollHeight + "px"; // ajusta ao conteúdo

        // contador
        counterComentario.textContent = textareaComentario.value.length;
        counterComentario.style.color = textareaComentario.value.length >= maxComentario ? "red" : "";
    });
});