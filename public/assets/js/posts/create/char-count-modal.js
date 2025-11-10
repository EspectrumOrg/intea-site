document.addEventListener("DOMContentLoaded", function () {
    const textareaCreateModal = document.getElementById("texto_postagem_create_modal");
    const counterCreateModal = document.getElementById("char-count-create-modal");
    const maxCreateModal = textareaCreateModal.getAttribute("maxlength");

    // Expande conforme digita
    textareaCreateModal.addEventListener("input", () => {
        textareaCreateModal.style.height = "auto"; // reseta antes de recalcular
        textareaCreateModal.style.height = textareaCreateModal.scrollHeight + "px"; // ajusta ao conteúdo

        // contador
        counterCreateModal.textContent = textareaCreateModal.value.length;
        counterCreateModal.style.color = textareaCreateModal.value.length >= maxCreateModal ? "red" : "";
    });
});