document.addEventListener("DOMContentLoaded", function () {
    const textareaComentarioFocus = document.getElementById("texto_resposta_comentario_focus");
    const counterComentarioFocus = document.getElementById("char-count-create-resposta_comentario_focus");
    const maxComentarioFocus = textareaComentarioFocus.getAttribute("maxlength");

    // Expande conforme digita
    textareaComentarioFocus.addEventListener("input", () => {
        textareaComentarioFocus.style.height = "auto"; // reseta antes de recalcular
        textareaComentarioFocus.style.height = textareaComentarioFocus.scrollHeight + "px"; // ajusta ao conteúdo

        // contador
        counterComentarioFocus.textContent = textareaComentarioFocus.value.length;
        counterComentarioFocus.style.color = textareaComentarioFocus.value.length >= maxComentarioFocus ? "red" : "";
    });
});