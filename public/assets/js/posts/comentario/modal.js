function abrirModalComentar() {
    document.getElementById('modal-comentar').classList.remove('hidden');
}

document.addEventListener("DOMContentLoaded", function () {
    const textareasComentar = document.querySelectorAll(".post-textarea-comentar");

    textareasComentar.forEach(textarea => {
        const counterComentar = textarea.closest("form").querySelector(".char-count-comentar");
        const maxComentar = parseInt(textarea.getAttribute("maxlength"), 10) || 280;

        function atualizar() {
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";

            counterComentar.textContent = textarea.value.length;
            counterComentar.style.color = textarea.value.length >= maxComentar ? "red" : "";
        }

        atualizar(); // inicializa
        textarea.addEventListener("input", atualizar);
    });
});

function fecharModalComentar() {
    const modalComentar = document.getElementById('modal-comentar');
    modalComentar.classList.add('hidden');

    const form = modalComentar.querySelector("form");
    if (form) {
        form.reset();

        // Zerar contador
        const counterComentar = form.querySelector(".char-count");
        if (counterComentar) counterComentar.textContent = "0";
    }
}
