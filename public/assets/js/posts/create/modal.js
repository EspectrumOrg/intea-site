function abrirModalPostar() {
    document.getElementById('modal-postar').classList.remove('hidden');
}

document.addEventListener("DOMContentLoaded", function () {
    const textareas = document.querySelectorAll(".post-textarea");

    textareas.forEach(textarea => {
        const counter = textarea.closest("form").querySelector(".char-count");
        const max = parseInt(textarea.getAttribute("maxlength"), 10) || 280;

        function atualizar() {
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";

            counter.textContent = textarea.value.length;
            counter.style.color = textarea.value.length >= max ? "red" : "";
        }

        atualizar(); // inicializa
        textarea.addEventListener("input", atualizar);
    });
});

function fecharModalPostar() {
    const modalPostar = document.getElementById('modal-postar');
    modalPostar.classList.add('hidden');

    const form = modalPostar.querySelector("form");
    if (form) {
        form.reset();

        // Zerar contador
        const counter = form.querySelector(".char-count");
        if (counter) counter.textContent = "0";
    }
}
