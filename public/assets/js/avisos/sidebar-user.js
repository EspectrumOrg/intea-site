document.addEventListener("DOMContentLoaded", () => {
    const dropdownContainer = document.getElementById("userDropdown");
    const dropdownMenu = dropdownContainer.querySelector(".dropdown-checar-perfil");
    // Abre/fecha ao clicar na área .info
    dropdownContainer.addEventListener("click", (e) => {
    e.stopPropagation(); // evita fechar ao clicar dentro
        dropdownMenu.classList.toggle("hidden");
    });

    // Fecha ao clicar fora
    document.addEventListener("click", () => {
        dropdownMenu.classList.add("hidden");
    });
});