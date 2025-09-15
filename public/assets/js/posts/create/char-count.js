document.addEventListener("DOMContentLoaded", function () {
    const textarea = document.getElementById("texto_postagem");
    const counter = document.getElementById("char-count");
    const max = textarea.getAttribute("maxlength");

    // Expande conforme digita
    textarea.addEventListener("input", () => {
        textarea.style.height = "auto"; // reseta antes de recalcular
        textarea.style.height = textarea.scrollHeight + "px"; // ajusta ao conteúdo

        // contador
        counter.textContent = textarea.value.length;
        counter.style.color = textarea.value.length >= max ? "red" : "";
    });
});