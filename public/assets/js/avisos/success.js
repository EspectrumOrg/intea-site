document.addEventListener("DOMContentLoaded", () => {
    const modais = document.querySelectorAll('.modal-aviso-padrao');

    modais.forEach(modal => {
        const closeBtn = modal.querySelector('.closeModalPadrao');

        // Fechar ao clicar no botão
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        /* Fechar automaticamente após 6s
        setTimeout(() => {
            modal.style.display = 'none';
        }, 6000);*/
    });
});
