function abrirModalPostar() {
    document.getElementById('modal-postar').classList.remove('hidden');
}

document.addEventListener("DOMContentLoaded", function () {
    const textareaModalPostar = document.querySelector(".post-textarea");
    const counterModalPostar = document.querySelector(".char-count");
    const previewModalPostar = document.getElementById("hashtag-preview-create-modal");
    const maxModalPostar = parseInt(textareaModalPostar.getAttribute("maxlength"), 10) || 280;

    textareaModalPostar.addEventListener("input", () => {
        // contador
        counterModalPostar.textContent = textareaModalPostar.value.length;
        counterModalPostar.style.color = textareaModalPostar.value.length >= maxModalPostar ? "red" : "";

        // hashtags
        const text = textareaModalPostar.value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/#(\w+)/g, '<span class="hashtag">#$1</span>');
        previewModalPostar.innerHTML = text;
    });
});

function fecharModalPostar() {
    const modalPostar = document.getElementById('modal-postar');
    modalPostar.classList.add('hidden');

    const formModalPostar = modalPostar.querySelector("form");
    if (formModalPostar) {
        formModalPostar.reset();

        // Zerar contador
        const counterModalPostar = formModalPostar.querySelector(".char-count");
        if (counterModalPostar) counterModalPostar.textContent = "0";

        // Limpar preview de hashtags
        const previewModalPostar = document.getElementById("hashtag-preview-create-modal");
        if (previewModalPostar) previewModalPostar.innerHTML = "";
    }
}
